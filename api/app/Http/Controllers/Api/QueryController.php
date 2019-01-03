<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Http\Resources\Resource;
use Dingo\Api\Routing\Helpers;
use Domain\Query\Query;
use Domain\Query\QueryImp;
use Domain\Query\QueryParams;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

//   protected function buildQueryIndex()
//   {
//      $build = array();
//      $aFields = $this->calculateFields();
//      if ($aFields[0] === '*') {
//         $this->setStatusCode(200);
//      } else {
//         $this->setStatusCode(206);
//      }
//      $build['fields'] = $aFields;
//
//      $aWiths = $this->getEagerLoad();
//
//      $skip = (int)$this->request->input('skip', 0);
//      $build['skip'] = $skip;
//      $limit = $this->calculateLimit();
//      $build['limit'] = $limit;
//
//      $aSort = $this->calculateSort();
//      $aDesc = $this->calculateSortDesc($aSort);
//
//      $query = $this->resource->model()->query()->with($aWiths);
//      foreach ($aSort as $sortBy) {
//         $query->when(
//            in_array($sortBy, $aDesc),
//            function ($pQ) use ($sortBy) {
//               return $pQ->orderBy($sortBy, 'desc');
//            },
//            function ($pQ) use ($sortBy) {
//               return $pQ->orderBy($sortBy, 'asc');
//            }
//         );
//      }
//      $build['query'] = $query;
//
//      return $build;
//   }

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
