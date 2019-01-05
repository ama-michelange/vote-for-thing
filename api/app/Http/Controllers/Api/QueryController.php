<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Http\Resources\Resource;
use Dingo\Api\Routing\Helpers;
use Domain\Query\Query;
use Domain\Query\QueryImp;
use Domain\Query\QueryParams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

abstract class QueryController extends BaseController
{
   use Helpers;

   /**
    * HTTP header status code.
    *
    * @var int
    */
   protected $statusCode = 200;

   /**
    * Resource instance.
    *
    * @var \App\Http\Resources\Resource;
    */
   protected $resource;

   /**
    * QueryEntity instance.
    *
    * @var Query;
    */
   protected $queryEntity;

   /**
    * Illuminate\Http\Request instance.
    *
    * @var Request
    */
   protected $request;

   /**
    * The parameters of the query.
    * @var QueryParams $queryParams
    */
   protected $queryParams;

   /**
    * Number of items displayed at once if not specified.
    * There is no limit if it is 0 or false.
    *
    * @var int|bool
    */
   protected $defaultLimit = false;

   /**
    * Maximum limit that can be set via $_GET['limit'].
    *
    * @var int|bool
    */
   protected $maximumLimit = false;

   /**
    * Constructor.
    *
    * @param Request $request
    */
   public function __construct(Request $request)
   {
      $this->request = $request;
      $this->resource = $this->resource();
      $this->queryEntity = new QueryImp($this->resource->entity());
   }

   /**
    * The resource associate to the query.
    * @return Resource
    */
   abstract protected function resource() : Resource;


   /**
    * List of the resource.
    * GET /api/{resource}.
    *
    * @return Response
    */
   public function index()
   {
      $this->queryParams = (new QueryParamsBuilder($this->request, $this->resource))->forFindCollection()->build();
      if ($this->queryParams->hasAllFields()) {
         $this->setStatusCode(200);
      } else {
         $this->setStatusCode(206);
      }
      $items = $this->queryEntity->findCollection($this->queryParams);
      return $this->respondWithCollection($items);
   }

   public function search()
   {
      $this->queryParams = (new QueryParamsBuilder($this->request, $this->resource))->forSearchCollection()->build();
      if ($this->queryParams->hasAllFields()) {
         $this->setStatusCode(200);
      } else {
         $this->setStatusCode(206);
      }
      $items = $this->queryEntity->findCollection($this->queryParams);
      return $this->respondWithCollection($items);
   }

   protected function getSearchable()
   {
      $searchable = $this->resource->model()->getFillable();
      $searchable[] = $this->resource->model()->getKeyName();
      if ($this->resource->model()->timestamps) {
         $searchable[] = $this->resource->model()->getCreatedAtColumn();
         $searchable[] = $this->resource->model()->getUpdatedAtColumn();
      }
      return $searchable;
   }

   /**
    * Specify relations for eager loading.
    *
    * @return array
    */
   protected function getEagerLoad()
   {
      $include = camel_case($this->request->input('include', ''));
      $includes = explode(',', $include);
      $includes = array_filter($includes);

      return $includes ?: [];
   }

   /**
    * Calcul des attributs de tri en fonction du paramètre 'sort' de la Query String.
    * @return array Le tableau contenant les attributs de tri
    */
   protected function calculateSort()
   {
      $asort = array();
      $sort = $this->request->input('sort');
      if (is_string($sort) && strlen($sort) > 0) {
         $asort = explode(',', $sort);
      }
      return $asort;
   }

   /**
    * Calcul des attributs de tri descendant en fonction du paramètre 'desc' de la Query String.
    * @param array [string] $paSort Le tableau des champs triés
    * @return array[string] Le tableau contenant les attributs de tri descendant
    */
   protected function calculateSortDesc($paSort)
   {
      $adesc = array();

      $desc = null;
      if ($this->request->exists('desc')) {
         $desc = $this->request->input('desc');
         if (is_null($desc)) {
            $desc = '';
         }
      }

      if (is_string($desc)) {
         if ($desc != '') {
            $adesc = explode(',', $desc);
         } elseif (count($paSort) > 0) {
            $adesc[] = $paSort[0];
         }
      }
      return $adesc;
   }

   /**
    * Calcul des champs à lire en fonction du paramètre 'fields' de la Query String.
    * @return array Le tableau contenant les champs à lire
    */
   protected function calculateFields()
   {
      $afields = ['*'];
      $fields = $this->request->input('fields');
      if (is_string($fields) && strlen($fields) > 0) {
         $afields = explode(',', $fields);
      }
      return $afields;
   }


   /**
    * Display the specified resource.
    * GET /api/{resource}/{id}.
    *
    * @param int $id
    *
    * @return Response
    */
   public function show($id)
   {
      $with = $this->getEagerLoad();
      $item = $this->findItem($id, $with);
      if (!$item) {
         return $this->errorNotFound();
      }
      $ret = $this->respondWithItem($item);
//      Log::debug("================================ DATA");
//      Log::debug('ama : '.print_r($ret->original, true));
      return $ret;
   }

   /**
    * Getter for statusCode.
    *
    * @return int
    */
   protected function getStatusCode()
   {
      return $this->statusCode;
   }

   /**
    * Setter for statusCode.
    *
    * @param int $statusCode Value to set
    *
    * @return self
    */
   protected function setStatusCode($statusCode)
   {
      $this->statusCode = $statusCode;

      return $this;
   }

   /**
    * Respond with a given item.
    *
    * @param $item
    *
    * @return mixed
    */
   protected function respondWithItem($item)
   {
      return $this->respondWithArray($this->resource->transformCollection($item, $this->queryParams));
   }

   /**
    * Respond with a given collection.
    *
    * @param $collection
    *     *
    * @return mixed
    */
   protected function respondWithCollection($collection)
   {
      return $this->respondWithArray($this->resource->transformCollection($collection, $this->queryParams));
   }

   /**
    * Respond with a given array of items.
    *
    * @param array $array
    * @param array $headers
    *
    * @return mixed
    */
   protected function respondWithArray(array $array, array $headers = [])
   {
      return response()->json($array, $this->statusCode, $headers);
   }

   /**
    * Response with the current error.
    *
    * @param string $message
    *
    * @return mixed
    */
   protected function respondWithError($message)
   {
      return $this->respondWithArray($this->resource->transformError($this->statusCode, $message));
   }


   /**
    * Generate a Response with a 403 HTTP header and a given message.
    *
    * @param $message
    *
    * @return Response
    */
   protected function errorForbidden($message = 'Forbidden')
   {
      return $this->setStatusCode(403)->respondWithError($message);
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
      return $this->setStatusCode(500)->respondWithError($message);
   }

   /**
    * Generate a Response with a 404 HTTP header and a given message.
    *
    * @param string $message
    *
    * @return Response
    */
   protected function errorNotFound($message = 'Resource Not Found')
   {
      return $this->setStatusCode(404)->respondWithError($message);
   }

   /**
    * Generate a Response with a 401 HTTP header and a given message.
    *
    * @param string $message
    *
    * @return Response
    */
   protected function errorUnauthorized($message = 'Unauthorized')
   {
      return $this->setStatusCode(401)->respondWithError($message);
   }

   /**
    * Generate a Response with a 400 HTTP header and a given message.
    *
    * @param string $message
    *
    * @return Response
    */
   protected function errorWrongArgs($message = 'Wrong Arguments')
   {
      return $this->setStatusCode(400)->respondWithError($message);
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
      return $this->setStatusCode(501)->respondWithError($message);
   }


   /**
    * Get item according to mode.
    *
    * @param int $id
    * @param array $with
    *
    * @return mixed
    */
   protected function findItem($id, array $with = [])
   {
      $afields = $this->calculateFields();
      if ($afields[0] === '*') {
         $this->setStatusCode(200);
      } else {
         $this->setStatusCode(206);
      }
      if ($this->request->has('use_as_id')) {
         return $this->resource->model()
            ->with($with)
            ->where($this->request->input('use_as_id'), '=', $id)
            ->first($afields);
      }

      return $this->resource->model()->with($with)->find($id, $afields);
   }

   /**
    * Calculates limit for a number of items displayed in list.
    *
    * @return int
    */
   protected function calculateLimit()
   {
      $limit = (int)$this->request->input('limit', $this->defaultLimit);

      return $this->maximumLimit && $this->maximumLimit < $limit ? $this->maximumLimit : $limit;
   }


}
