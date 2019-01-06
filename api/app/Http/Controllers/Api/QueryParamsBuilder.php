<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Resource;
use Domain\Query\QueryParams;
use Domain\Query\QueryParamsImp;
use Illuminate\Http\Request;

/**
 * Class QueryParamsBuilder to build the parameters of a query with the Query String parameters.
 * @see QueryParams
 */
class QueryParamsBuilder
{
   protected $limit;
   protected $skip;
   protected $aField;
   protected $aInclude;
   protected $aSort;
   protected $aDesc;
   protected $aSearch;
   protected $aUseAsId;

   /**
    * Illuminate\Http\Request instance.
    *
    * @var Request
    */
   protected $request;

   /**
    * Resource instance.
    *
    * @var \App\Http\Resources\Resource
    */
   protected $resource;

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
    * @param Request $request The current request with the query string to analyse
    * @param Resource $resource The resource where searching, null by default for not search
    */
   public function __construct(Request $request, Resource $resource = null)
   {
      $this->request = $request;
      $this->resource = $resource;
   }

   /**
    * Short-cut to get the parameters 'limit', 'skip', 'field', 'include', 'sort' and 'desc' in the Query String.
    * @return QueryParamsBuilder
    * @see QueryParamsBuilder::withField()
    * @see QueryParamsBuilder::withInclude()
    * @see QueryParamsBuilder::withLimit()
    * @see QueryParamsBuilder::withSkip()
    * @see QueryParamsBuilder::withSortDesc()
    */
   public function forFindCollection() : QueryParamsBuilder
   {
      return $this->withField()->withInclude()->withLimit()->withSkip()->withSortDesc();
   }

   /**
    * Short-cut to get all searching parameters in the Query String.
    * <p>After call {@link QueryParamsBuilder::forFindCollection()}.</p>
    * @return QueryParamsBuilder
    * @see QueryParamsBuilder::withSearch()
    * @see QueryParamsBuilder::forFindCollection()
    */
   public function forSearchCollection() : QueryParamsBuilder
   {
      return $this->withSearch()->forFindCollection();
   }

   /**
    * Short-cut to get the parameters 'field', 'include', 'use_as_id' in the Query String.
    * @param string|int $id The identifier to find
    * @return QueryParamsBuilder
    * @see QueryParamsBuilder::withField()
    * @see QueryParamsBuilder::withInclude()
    * @see QueryParamsBuilder::withUseAsId()
    */
   public function forFindItem($id) : QueryParamsBuilder
   {
      return $this->withField()->withInclude()->withUseAsId($id);
   }

   /**
    * Build a {@link QueryParams QueryParams} with all specified elements.
    * @see QueryParamsBuilder::forFindCollection()
    * @see QueryParamsBuilder::withField()
    * @see QueryParamsBuilder::withInclude()
    * @see QueryParamsBuilder::withLimit()
    * @see QueryParamsBuilder::withSkip()
    * @see QueryParamsBuilder::withSort()
    * @see QueryParamsBuilder::withSortDesc()
    * @see QueryParamsBuilder::withSearch()
    * @see QueryParamsBuilder::withUseAsId()
    * @return QueryParams
    */
   public function build() : QueryParams
   {
      $qParams = new QueryParamsImp();
      if (isset($this->limit)) {
         $qParams->put(QueryParams::LIMIT, $this->limit);
      }
      if (isset($this->skip)) {
         $qParams->put(QueryParams::SKIP, $this->skip);
      }
      if (isset($this->aField)) {
         $qParams->put(QueryParams::FIELD, $this->aField);
      }
      if (isset($this->aInclude)) {
         $qParams->put(QueryParams::INCLUDE, $this->aInclude);
      }
      if (isset($this->aSort)) {
         $qParams->put(QueryParams::SORT, $this->aSort);
      }
      if (isset($this->aDesc)) {
         $qParams->put(QueryParams::DESC, $this->aDesc);
      }
      if (isset($this->aSearch)) {
         $qParams->put(QueryParams::SEARCH, $this->aSearch);
      }
      if (isset($this->aUseAsId)) {
         $qParams->put(QueryParams::USE_AS_ID, $this->aUseAsId);
      }
      return $qParams;
   }

   /**
    * Get the 'field' parameter of the Query String.
    * <p>This parameter should be a string with each element separated by a comma if necessary.</p>
    * <p>The values should be a known field of the resource object.</p>
    * @return QueryParamsBuilder
    */
   public function withField(): QueryParamsBuilder
   {
      $fields = $this->request->input(QueryParams::FIELD);
      $aField = $this->splitStringComma($fields);
      if (count($aField) > 0) {
         $this->aField = $aField;
      } else {
         $this->aField = ['*'];
      }
      return $this;
   }

   /**
    * Get the 'include' parameter of the Query String.
    * <p>This parameter should be a string with each element separated by a comma if necessary.</p>
    * <p>The values should be a known included object of the resource object.
    * Each value is converted to a camel case string.</p>
    * @return QueryParamsBuilder
    */
   public function withInclude(): QueryParamsBuilder
   {
      $include = $this->request->input(QueryParams::INCLUDE, '');
      $aInclude = $this->splitStringComma($include);
      if (count($aInclude) > 0) {
         $aInclude = array_map(function ($value) {
            return camel_case($value);
         }, $aInclude);
         $this->aInclude = $aInclude;
      }
      return $this;
   }

   /**
    * Get the 'limit' parameter of the Query String.
    * <p>This parameter should be a integer.</p>
    * <p>If this parameter don't exist in the Query String the value will be the same value of '{@link QueryParamsBuilder::defaultLimit defaultLimit}' or 0.</p>
    * <p>If the property '{@link QueryParamsBuilder::maximumLimit maximumLimit}' exist The limit will never be bigger.</p>
    * @return QueryParamsBuilder
    */
   public function withLimit(): QueryParamsBuilder
   {
      $limit = (int)$this->request->input(QueryParams::LIMIT, $this->defaultLimit);
      $this->limit = $this->maximumLimit && $this->maximumLimit < $limit ? $this->maximumLimit : $limit;
      return $this;
   }

   /**
    * Get the 'skip' parameter of the Query String.
    * <p>This parameter should be a integer.
    * If this parameter don't exist in the Query String the value will be 0.</p>
    * @return QueryParamsBuilder
    */
   public function withSkip(): QueryParamsBuilder
   {
      $this->skip = (int)$this->request->input(QueryParams::SKIP, 0);
      return $this;
   }

   /**
    * Get the 'sort' parameter of the Query String.
    * <p>This parameter should be a string with each element separated by a comma if necessary.</p>
    * <p>The values should be a known field of the resource object.</p>
    * @return QueryParamsBuilder
    */
   public function withSort() : QueryParamsBuilder
   {
      $sort = $this->request->input(QueryParams::SORT, '');
      $aSort = $this->splitStringComma($sort);
      if (count($aSort) > 0) {
         $this->aSort = $aSort;
      }
      return $this;
   }

   /**
    * Get the 'desc' and 'sort' parameters of the Query String.
    * <p>This parameters should be a string with each element separated by a comma if necessary.</p>
    * <p>The values should be a known field of the resource object.</p>
    * @return QueryParamsBuilder
    */
   public function withSortDesc() : QueryParamsBuilder
   {
      if (false == isset($this->aSort)) {
         $this->withSort();
         if (false == isset($this->aSort)) {
            return $this;
         }
      }
      if ($this->request->has(QueryParams::DESC)) {
         $desc = $this->request->input(QueryParams::DESC, '');
         $aDesc = $this->splitStringComma($desc);
         if (count($aDesc) > 0) {
            $this->aDesc = $aDesc;
         } elseif (count($this->aSort) > 0) {
            $this->aDesc[] = $this->aSort[0];
         }
      }
      return $this;
   }

   /**
    * Get all the parameters and its values in the query string that have the same name as an entity attribute.
    * @return QueryParamsBuilder
    */
   public function withSearch(): QueryParamsBuilder
   {
      if (isset($this->resource)) {
         $entity = $this->resource->entity();
         $this->aSearch = $this->request->only($entity->getVisible());
      }
      return $this;
   }

   /**
    * Get the 'use_as_id' parameter of the Query String.
    * <p>This parameter should be a string.</p>
    * <p>The values should be a known field of the resource object.</p>
    * @param string|int $id The identifier to find
    * @return QueryParamsBuilder
    */
   public function withUseAsId($id): QueryParamsBuilder
   {
      $useAsId = $this->request->input(QueryParams::USE_AS_ID, false);
      if ($useAsId) {
         $this->aUseAsId = [$useAsId, $id];
      }
      return $this;
   }

   /**
    * Split a string with comma separator to an array.
    * <p>The space before and after each element between comma are removed.</p>
    * @param string $pStringComma The string with comma
    * @return array
    */
   protected function splitStringComma($pStringComma): array
   {
      $ret = [];
      if (is_string($pStringComma) && strlen($pStringComma) > 0) {
         $aString = explode(',', $pStringComma);
         // Remove empty value
         $aString = array_filter($aString, function ($value) {
            if (is_string($value)) {
               if (strlen(trim($value)) > 0) {
                  return true;
               }
            }
            return false;
         });
         // Trim each array values
         $ret = array_map(function ($value) {
            return trim($value);
         }, $aString);
         // Merge to get a linear array
         $ret = array_merge([], $ret);
      }
      return $ret;
   }
}
