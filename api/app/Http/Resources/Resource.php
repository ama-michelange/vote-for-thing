<?php

namespace App\Http\Resources;

use Domain\Command\Command;
use Domain\Entity\Entity;
use Domain\Query\Query;
use Domain\Query\QueryParams;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;
use League\Fractal\Manager;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\DataArraySerializer;

abstract class Resource
{
   /**
    * Entity domain instance.
    *
    * @var \Domain\Entity\Entity;
    */
   protected $entity;

   /**
    * The Query instance of the domain.
    *
    * @var Query;
    */
   protected $query;

   /**
    * The Command instance of the domain.
    *
    * @var Command;
    */
   protected $command;

   /**
    * Fractal Manager instance.
    *
    * @var Manager
    */
   protected $fractal;

   /**
    * Fractal Transformer instance.
    *
    * @var \League\Fractal\TransformerAbstract
    */
   protected $transformer;

//
//   /**
//    * The parameters of the query.
//    *
//    * @var QueryParams
//    */
//   protected $queryParams;

//   /**
//    * Do we need to unguard the model before create/update?
//    *
//    * @var bool
//    */
//   protected $unguard = false;

   /**
    * Resource key for an item.
    *
    * @var string
    */
   protected $keySingular = 'data';

   /**
    * Resource key for a collection.
    *
    * @var string
    */
   protected $keyPlural = 'data';

   /**
    * Constructor.
    */
   public function __construct()
   {
      $this->transformer = $this->transformer();
      $this->fractal = new Manager();
      $this->fractal->setSerializer($this->serializer());
   }

   /**
    * Entity domain instance.
    *
    * @return Entity;
    */
   public function entity() : Entity
   {
      return $this->entity;
   }


   /**
    * The Query instance of the domain for this resource.
    *
    * @return Query;
    */
   public function query() : Query
   {
      return $this->query;
   }

   /**
    * The Command instance of the domain for this resource.
    *
    * @return Command;
    */
   public function command() : Command
   {
      return $this->command;
   }

   /**
    * Transformer for the current resource.
    *
    * @return \League\Fractal\TransformerAbstract
    */
   abstract protected function transformer();

   /**
    * All field names that can use.
    *
    * @return array;
    */
   public function fields() : array
   {
      return $this->entity()->getVisible();
   }

   /**
    * Serializer for the current resource.
    *
    * @return \League\Fractal\Serializer\SerializerAbstract
    */
   protected function serializer()
   {
      return new DataArraySerializer();
   }

   /**
    * Resource key for an item.
    * @return string
    */
   public function keySingular() : string
   {
      return $this->keySingular;
   }

   /**
    * Resource key for a collection.
    * @return string
    */
   public function keyPlural() : string
   {
      return $this->keyPlural;
   }

   /**
    * Transform a model item to a resource item.
    *
    * @param $item
    * @param QueryParams $queryParams
    * @return array
    */
   public function transformItem($item, $queryParams)
   {
      $resource = new Item($item, $this->transformer, $this->keySingular);
      $rootScope = $this->prepareRootScope($resource, $queryParams);
      return $rootScope->toArray();
   }

   /**
    * Transform a model collection to a resource collection.
    *
    * @param $collection
    * @param QueryParams $queryParams
    *
    * @return array
    */
   public function transformCollection($collection, $queryParams)
   {
      $resource = new Collection($collection, $this->transformer, $this->keyPlural);

      if ($queryParams->hasLimit()) {
         $limit = $queryParams->getInt(QueryParams::LIMIT);
         $skip = $queryParams->getInt(QueryParams::SKIP);
         $prev = $skip - $limit < 0 ? $skip : $skip - $limit;
         $next = $collection->count() < $skip ? $skip : $skip + $limit;
         $cursor = new Cursor($skip, $prev, $next, $collection->count());
         $resource->setCursor($cursor);
      }

      $rootScope = $this->prepareRootScope($resource, $queryParams);

      return $rootScope->toArray();
   }


   /**
    * Transform a error to a resource error.
    * @param int $statusCode
    * @param string $message
    * @return array
    */
   public function transformError($statusCode, $message)
   {
      return [
         'error' => [
            'http_code' => $statusCode,
            'message' => $message
         ]
      ];
   }

   /**
    * Prepare root scope and set some meta information.
    *
    * @param Item|Collection $resource
    *
    * @param QueryParams $queryParams
    * @return \League\Fractal\Scope
    */
   protected function prepareRootScope($resource, $queryParams)
   {
      if ($queryParams->has(QueryParams::INCLUDE)) {
         $this->fractal->parseIncludes($queryParams->getArray(QueryParams::INCLUDE));
      }
//      $resource->setMetaValue('available_includes', $this->transformer->getAvailableIncludes());
//      $resource->setMetaValue('default_includes', $this->transformer->getDefaultIncludes());

      return $this->fractal->createData($resource);
   }

   /**
    * Get the validation rules for create.
    *
    * @return array
    */
   public function rulesForCreate()
   {
      return [];
   }

   /**
    * Get the validation rules for update.
    *
    * @param int $id
    *
    * @return array
    */
   public function rulesForUpdate($id)
   {
      return [];
   }

   /**
    * Generate a Response with a 403 HTTP header and a given message.
    *
    * @param $message
    *
    * @return Response
    */
   public function errorForbidden($message = 'Forbidden')
   {
      return $this->transformError(403, $message);
   }

   /**
    * Generate a Response with a 500 HTTP header and a given message.
    *
    * @param string $message
    *
    * @return Response
    */
   protected function errorInternalError($message = 'Internal Error')
   {
      return $this->transformError(500, $message);
   }

   /**
    * Generate a Response with a 404 HTTP header and a given message.
    *
    * @param string $message
    *
    * @return Response
    */
   public function errorNotFound($message = 'Resource Not Found')
   {
      return $this->transformError(404, $message);
   }

   /**
    * Generate a Response with a 401 HTTP header and a given message.
    *
    * @param string $message
    *
    * @return Response
    */
   public function errorUnauthorized($message = 'Unauthorized')
   {
      return $this->transformError(401, $message);
   }

   /**
    * Generate a Response with a 400 HTTP header and a given message.
    *
    * @param string $message
    *
    * @return Response
    */
   public function errorWrongArgs($message = 'Wrong Arguments')
   {
      return $this->transformError(400, $message);
   }

   /**
    * Generate a Response with a 501 HTTP header and a given message.
    *
    * @param string $message
    *
    * @return Response
    */
   protected function errorNotImplemented($message = 'Not implemented')
   {
      return $this->transformError(501, $message);
   }


   /**
    * Transforme si nécessaire les données en provenance de la requête avant la sauvegarde dans la base de données.
    * @param array $pData Les données en provenance de la requête (POST ou PUT)
    * @param Model | null $pItem Les données en provenance de la base de données (PUT)
    * @return array Les données transformées prêtes à sauvegarder
    */
   public function transformBeforeSave($pData, $pItem = null)
   {
      return $pData;
   }

   /**
    * Récupère la valeur de la clé donnée en commancant à chercher dans le tableau de données puis dans l'objet de modèle si non trouvé.
    * @param string $pKey La clé de la valeur à chercher
    * @param array $pData Le tableau de données
    * @param Model | null $pItem L'objet de modèle
    * @return mixed | null La valeur trouvée ou nul si non trouvé
    */
   protected function getValue($pKey, $pData, $pItem = null)
   {
      $value = null;
      if (isset($pData[$pKey])) {
         $value = $pData[$pKey];
      }
      if (null == $value && isset($pItem->$pKey)) {
         $value = $pItem->$pKey;
      }
      return $value;
   }
}
