<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Manager;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\DataArraySerializer;

abstract class ApiController extends LaravelController
{
   /**
    * HTTP header status code.
    *
    * @var int
    */
   protected $statusCode = 200;

   /**
    * Fractal Manager instance.
    *
    * @var Manager
    */
   protected $fractal;

   /**
    * Eloquent model instance.
    *
    * @var \Illuminate\Database\Eloquent\Model;
    */
   protected $model;

   /**
    * Fractal Transformer instance.
    *
    * @var \League\Fractal\TransformerAbstract
    */
   protected $transformer;

   /**
    * Illuminate\Http\Request instance.
    *
    * @var Request
    */
   protected $request;

   /**
    * Do we need to unguard the model before create/update?
    *
    * @var bool
    */
   protected $unguard = false;

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
    * Resource key for an item.
    *
    * @var string
    */
   protected $resourceKeySingular = 'data';

   /**
    * Resource key for a collection.
    *
    * @var string
    */
   protected $resourceKeyPlural = 'data';

   /**
    * Constructor.
    *
    * @param Request $request
    */
   public function __construct(Request $request)
   {
      $this->model = $this->model();
      $this->transformer = $this->transformer();

      $this->fractal = new Manager();
      $this->fractal->setSerializer($this->serializer());

      $this->request = $request;

      if ($this->request->has('include')) {
         $this->fractal->parseIncludes(camel_case($this->request->input('include')));
      }
   }

   /**
    * Eloquent model.
    *
    * @return \Illuminate\Database\Eloquent\Model
    */
   abstract protected function model();

   /**
    * Transformer for the current model.
    *
    * @return \League\Fractal\TransformerAbstract
    */
   abstract protected function transformer();

   /**
    * Serializer for the current model.
    *
    * @return \League\Fractal\Serializer\SerializerAbstract
    */
   protected function serializer()
   {
      return new DataArraySerializer();
   }

   /**
    * List of the resource.
    * GET /api/{resource}.
    *
    * @return Response
    */
   public function index()
   {
      $build = $this->buildQueryIndex();
      $items = $build['limit']
         ? $build['query']
            ->skip($build['skip'])
            ->limit($build['limit'])
            ->get($build['fields'])
         : $build['query']->get($build['fields']);
      return $this->respondWithCollection($items, $build['skip'], $build['limit']);
   }

   protected function buildQueryIndex()
   {
      $build = array();
      $aFields = $this->calculateFields();
      if ($aFields[0] === '*') {
         $this->setStatusCode(200);
      } else {
         $this->setStatusCode(206);
      }
      $build['fields'] = $aFields;

      $aWiths = $this->getEagerLoad();

      $skip = (int)$this->request->input('skip', 0);
      $build['skip'] = $skip;
      $limit = $this->calculateLimit();
      $build['limit'] = $limit;

      $aSort = $this->calculateSort();
      $aDesc = $this->calculateSortDesc($aSort);

      $query = $this->model->query()->with($aWiths);
      foreach ($aSort as $sortBy) {
         $query->when(
            in_array($sortBy, $aDesc),
            function ($pQ) use ($sortBy) {
               return $pQ->orderBy($sortBy, 'desc');
            },
            function ($pQ) use ($sortBy) {
               return $pQ->orderBy($sortBy, 'asc');
            }
         );
      }
      $build['query'] = $query;

      return $build;
   }

   public function search()
   {
      // Log::debug("================================ search ");
      // Log::debug("Request->keys = " . implode(",", $this->request->keys()));
      // Log::debug("Request->fullUrl = " . $this->request->fullUrl());
      $build = $this->buildQuerySearch();
      if (isset($build['error'])) {
         return $this->errorWrongArgs([
            'type' => 'Wrong Arguments',
            'detail' => $build['error']
         ]);
      }
      //        $debug = $build['limit']
      //            ? $build['query']->skip($build['skip'])->limit($build['limit'])->toSql()
      //            : $build['query']->toSql();
      //        Log::debug("SQL : $debug");
      $items = $build['limit']
         ? $build['query']
            ->skip($build['skip'])
            ->limit($build['limit'])
            ->get($build['fields'])
         : $build['query']->get($build['fields']);
      return $this->respondWithCollection($items, $build['skip'], $build['limit']);
   }

   protected function buildQuerySearch()
   {
      $aSearch = $this->calculateSearch();
      Log::debug("aSearch : " . json_encode($aSearch));
      if (empty($aSearch)) {
         return array('error' => 'No known field !');
      }
      $aWheres = array();
      $build = $this->buildQueryIndex();
      $query = $build['query'];

      foreach ($aSearch as $field => $expr) {
         if (is_array($expr)) {
            // Log::debug(">>>>> buildQuerySearch : field = [$field], ARRAY expr = " . implode(',', $expr));
            foreach ($expr as $exprSameField) {
               $aWheres[] = $this->calculateWhere($field, $exprSameField);
            }
         } else {
            // Log::debug(">>>>> buildQuerySearch : field = [$field], STRING expr = [$expr]");
            $aWheres[] = $this->calculateWhere($field, $expr);
         }
      }
      $this->addWhere($query, $aWheres);
      return $build;
   }

   /**
    * @param Builder $pQuery
    * @param array $paWheres
    */
   protected function addWhere($pQuery, $paWheres)
   {
      // Log::debug(">>>>> addWhere");
      foreach ($paWheres as $awhere) {
         // Log::debug("=== awhere : " . json_encode($awhere));
         if (isset($awhere['column2'])) {
            $pQuery->whereColumn($awhere['column'], $awhere['operator'], $awhere['column2'], $awhere['boolean']);
         } elseif (isset($awhere['date'])) {
            switch ($awhere['date']) {
               case 'date':
                  $pQuery->whereDate($awhere['column'], $awhere['operator'], $awhere['value'], $awhere['boolean']);
                  break;
               case 'day':
                  $pQuery->whereDay(
                     $awhere['column'],
                     $awhere['operator'],
                     str_pad($awhere['value'], 2, '0', STR_PAD_LEFT),
                     $awhere['boolean']
                  );
                  break;
               case 'month':
                  $pQuery->whereMonth(
                     $awhere['column'],
                     $awhere['operator'],
                     str_pad($awhere['value'], 2, '0', STR_PAD_LEFT),
                     $awhere['boolean']
                  );
                  break;
               case 'year':
                  $pQuery->whereYear($awhere['column'], $awhere['operator'], $awhere['value'], $awhere['boolean']);
                  break;
               case 'time':
                  $pQuery->whereTime($awhere['column'], $awhere['operator'], $awhere['value'], $awhere['boolean']);
                  break;
            }
         } else {
            switch ($awhere['operator']) {
               case '=':
               case '>':
               case '<':
               case '>=':
               case '<=':
               case '<>':
               case 'like':
                  $pQuery->where($awhere['column'], $awhere['operator'], $awhere['value'], $awhere['boolean']);
                  break;
               case 'null':
                  $pQuery->whereNull($awhere['column'], $awhere['boolean'], $awhere['not']);
                  break;
               case 'in':
                  $pQuery->whereIn($awhere['column'], $awhere['value'], $awhere['boolean'], $awhere['not']);
                  break;
               case 'between':
                  $pQuery->whereBetween($awhere['column'], $awhere['value'], $awhere['boolean'], $awhere['not']);
                  break;
            }
         }
      }
   }

   protected function calculateWhere($pField, $pExpr)
   {
      // Log::debug(">>>>> calculateWhere : pField = [$pField], pExpr = [$pExpr]");
      $aWhere = null;
      if (is_string($pExpr) && strlen($pExpr) > 0) {
         $aExpr = explode(' ', $pExpr);
         $aWhere = array(
            'column' => $pField,
            'operator' => '=',
            'value' => null,
            'boolean' => 'and',
            'not' => false
         );
         $index = 0;
         foreach ($aExpr as $expr) {
            $aWhere = $this->calculateWhereExpression($aWhere, $expr, $index);
            $index++;
         }
      }
      // Log::debug('====== calculateWhere : aWhere = ' . json_encode($aWhere));
      return $aWhere;
   }

   protected function calculateWhereExpression($pArray, $pExpr, $pIndex)
   {
      // Log::debug(">>>>> calculateWhereExpression");
      // Log::debug("pArray = " . json_encode($pArray));
      // Log::debug("pExpr = $pExpr");
      // Log::debug("pIndex = $pIndex");

      $expr = trim($pExpr);
      if ($pIndex === 0) {
         if (strtolower($expr) === 'or') {
            $pArray['boolean'] = 'or';
         } elseif (strtolower($expr) === 'not') {
            $pArray['not'] = true;
         } elseif ($this->isWhereDate($expr)) {
            $pArray['date'] = $expr;
         } elseif ($this->isWhereOperator($expr)) {
            $pArray['operator'] = $this->toWhereOperatorEnabled($expr);
         } else {
            $pArray = $this->calculateWhereValue($pArray, $expr);
         }
      } elseif ($pIndex === 1) {
         if (strtolower($expr) === 'not') {
            $pArray['not'] = true;
         } elseif ($this->isWhereDate($expr)) {
            $pArray['date'] = $expr;
         } elseif ($this->isWhereOperator($expr)) {
            $pArray['operator'] = $this->toWhereOperatorEnabled($expr);
         } else {
            $pArray = $this->calculateWhereValue($pArray, $expr);
         }
      } elseif ($pIndex === 2) {
         if ($this->isWhereDate($expr)) {
            $pArray['date'] = $expr;
         } elseif ($this->isWhereOperator($expr)) {
            $pArray['operator'] = $this->toWhereOperatorEnabled($expr);
         } else {
            $pArray = $this->calculateWhereValue($pArray, $expr);
         }
      } elseif ($pIndex === 3) {
         if ($this->isWhereOperator($expr)) {
            $pArray['operator'] = $this->toWhereOperatorEnabled($expr);
         } else {
            $pArray = $this->calculateWhereValue($pArray, $expr);
         }
      } else {
         $pArray = $this->calculateWhereValue($pArray, $expr);
      }
      return $pArray;
   }

   protected function isWhereOperator($pExpr)
   {
      $expr = strtolower($pExpr);
      $ret = false;
      switch ($expr) {
         case '=':
         case '<>':
         case '!=':
         case '<':
         case '>':
         case '<=':
         case '>=':
         case 'like':
         case 'null':
         case 'in':
         case 'between':
            $ret = true;
            break;
      }
      return $ret;
   }

   protected function isWhereDate($pExpr)
   {
      $expr = strtolower($pExpr);
      $ret = false;
      switch ($expr) {
         case 'date':
         case 'day':
         case 'month':
         case 'year':
         case 'time':
            $ret = true;
            break;
      }
      return $ret;
   }

   protected function toWhereOperatorEnabled($pExpr)
   {
      $ope = strtolower($pExpr);
      switch ($ope) {
         case '!=':
            $ope = '<>';
            break;
      }
      return $ope;
   }

   protected function calculateWhereValue($pArray, $pExpr)
   {
      $pos = strpos($pExpr, '*');
      if ($pos !== false) {
         $pos = strpos($pExpr, '**');
         if ($pos !== false) {
            $pArray['value'] = str_replace('**', '%', $pExpr);
            $pArray['operator'] = 'like';
         } else {
            $pArray['value'] = str_replace('*', '%', $pExpr);
            $pArray['operator'] = 'like';
         }
         return $pArray;
      }
      $pos = strpos($pExpr, '[');
      $pos2 = strpos($pExpr, ']');
      if ($pos !== false && $pos2 !== false) {
         if ($pos === 0 && $pos2 === strlen($pExpr) - 1) {
            $exp = substr($pExpr, 1, $pos2 - 1);
            $pArray['value'] = explode(',', $exp);
            return $pArray;
         }
      }
      $pos = strpos($pExpr, '(');
      $pos2 = strpos($pExpr, ')');
      if ($pos !== false && $pos2 !== false) {
         if ($pos === 0 && $pos2 === strlen($pExpr) - 1) {
            $exp = substr($pExpr, 1, $pos2 - 1);
            $pArray['value'] = explode(',', $exp);
            return $pArray;
         }
      }

      if (in_array($pExpr, $this->getSearchable(), true)) {
         $pArray['column2'] = $pExpr;
      } else {
         $pArray['value'] = $pExpr;
      }
      return $pArray;
   }

   /**
    * Calcul des attributs de recherche en fonction des paramètres du champ du modèle de la Query String.
    * @return array Le tableau contenant les attributs de recherche brut
    */
   protected function calculateSearch()
   {
      return $this->request->only($this->getSearchable());
   }

   protected function getSearchable()
   {
      $searchable = $this->model->getFillable();
      $searchable[] = $this->model->getKeyName();
      if ($this->model->timestamps) {
         $searchable[] = $this->model->getCreatedAtColumn();
         $searchable[] = $this->model->getUpdatedAtColumn();
      }
      Log::debug("searchable : " . print_r($searchable, true));
      return $searchable;
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

   private function traceVarDump($pMixed = null, $pMessage = null)
   {
      Log::debug("================================================================================");
      if ($pMessage) {
         Log::debug($pMessage);
      }
      Log::debug($this->var_dump_ret($pMixed));
      Log::debug("================================================================================");
   }

   private function var_dump_ret($mixed = null)
   {
      ob_start();
      var_dump($mixed);
      $content = ob_get_contents();
      ob_end_clean();
      return $content;
   }

   /**
    * Store a newly created resource in storage.
    * POST /api/{resource}.
    *
    * @return Response
    */
   public function store()
   {
      $data = $this->request->json()->get($this->resourceKeySingular);

      if (!$data) {
         return $this->errorWrongArgs('Empty data');
      }

      $validator = Validator::make($data, $this->rulesForCreate());
      if ($validator->fails()) {
         return $this->errorWrongArgs($validator->messages());
      }

      $data = $this->transformBeforeSave($data);
      $this->unguardIfNeeded();

      $item = $this->model->create($data);
      return $this->setStatusCode(201)->respondWithItem($item);
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
//      Log::debug("================================ WITH");
//      Log::debug(print_r($with, true));

      $item = $this->findItem($id, $with);
      if (!$item) {
         return $this->errorNotFound();
      }
//      Log::debug("================================ ITEM");
//      Log::debug(print_r($item, true));

      $ret = $this->respondWithItem($item);
//      Log::debug("================================ DATA");
      Log::debug(print_r($ret->original, true));
      return $ret;
   }

   /**
    * Update the specified resource in storage.
    * PUT /api/{resource}/{id}.
    *
    * @param int $id
    *
    * @return Response
    */
   public function update($id)
   {
      $data = $this->request->json()->get($this->resourceKeySingular);

      if (!$data) {
         return $this->errorWrongArgs('Empty data');
      }

      $item = $this->findItem($id);
      if (!$item) {
         return $this->errorNotFound();
      }

      $validator = Validator::make($data, $this->rulesForUpdate($item->id));
      if ($validator->fails()) {
         return $this->errorWrongArgs($validator->messages());
      }

      $data = $this->transformBeforeSave($data, $item);
      $this->unguardIfNeeded();

      $item->fill($data);
      $item->save();

      return $this->respondWithItem($item);
   }

   /**
    * Remove the specified resource from storage.
    * DELETE /api/{resource}/{id}.
    *
    * @param int $id
    *
    * @return Response
    */
   public function destroy($id)
   {
      $item = $this->findItem($id);

      if (!$item) {
         return $this->errorNotFound();
      }

      $item->delete();

      return response(null, 204);
   }

   /**
    * Show the form for creating the specified resource.
    *
    * @return Response
    */
   public function create()
   {
      return $this->errorNotImplemented();
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param int $id
    *
    * @return Response
    */
   public function edit($id)
   {
      return $this->errorNotImplemented();
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
      $resource = new Item($item, $this->transformer, $this->resourceKeySingular);
      $rootScope = $this->prepareRootScope($resource);
      return $this->respondWithArray($rootScope->toArray());
   }

   /**
    * Respond with a given collection.
    *
    * @param $collection
    * @param int $skip
    * @param int $limit
    *
    * @return mixed
    */
   protected function respondWithCollection($collection, $skip = 0, $limit = 0)
   {
      $resource = new Collection($collection, $this->transformer, $this->resourceKeyPlural);

      if ($limit) {
         $prev = $skip - $limit < 0 ? $skip : $skip - $limit;
         $next = $collection->count() < $skip ? $skip : $skip + $limit;
         $cursor = new Cursor($skip, $prev, $next, $collection->count());
         $resource->setCursor($cursor);
      }

      $rootScope = $this->prepareRootScope($resource);

      return $this->respondWithArray($rootScope->toArray());
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
      return $this->respondWithArray([
         'error' => [
            'http_code' => $this->statusCode,
            'message' => $message
         ]
      ]);
   }

   /**
    * Prepare root scope and set some meta information.
    *
    * @param Item|Collection $resource
    *
    * @return \League\Fractal\Scope
    */
   protected function prepareRootScope($resource)
   {
      $resource->setMetaValue('available_includes', $this->transformer->getAvailableIncludes());
      $resource->setMetaValue('default_includes', $this->transformer->getDefaultIncludes());

      return $this->fractal->createData($resource);
   }

   /**
    * Get the validation rules for create.
    *
    * @return array
    */
   protected function rulesForCreate()
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
   protected function rulesForUpdate($id)
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
         return $this->model
            ->with($with)
            ->where($this->request->input('use_as_id'), '=', $id)
            ->first($afields);
      }

      return $this->model->with($with)->find($id, $afields);
   }

   /**
    * Unguard eloquent model if needed.
    */
   protected function unguardIfNeeded()
   {
      if ($this->unguard) {
         $this->model->unguard();
      }
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

   /**
    * Transforme si nécessaire les données en provenance de la requête avant la sauvegarde dans la base de données.
    * @param array $pData Les données en provenance de la requête (POST ou PUT)
    * @param Model | null $pItem Les données en provenance de la base de données (PUT)
    * @return array Les données transformées prêtes à sauvegarder
    */
   protected function transformBeforeSave($pData, $pItem = null)
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
