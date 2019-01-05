<?php

namespace Infra\Query;

use Domain\Entity\Entity;
use Domain\InfraBuilder;
use Domain\Query\QueryEntityBuilder;
use Domain\Query\QueryParams;
use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Infra\EloquentBuilder;

class QueryEntityEloquentBuilder implements QueryEntityBuilder
{
   /**
    * Entity instance.
    *
    * @var Entity;
    */
   protected $entity;

   /**
    * Infra builder instance.
    *
    * @var InfraBuilder;
    */
   protected $infraBuilder;

   /**
    * @var QueryParams
    */
   protected $queryParams;

   /**
    * Constructor.
    *
    * @param Entity $entity
    * @param InfraBuilder $infraBuilder
    */
   public function __construct(Entity $entity, InfraBuilder $infraBuilder = null)
   {
      $this->entity = $entity;
      $this->infraBuilder = $this->createInfraBuilder($infraBuilder);
   }

   /**
    * {@inheritdoc}
    */
   public function withParams($pQueryParams) : QueryEntityBuilder
   {
      $this->queryParams = $pQueryParams;
      return $this;
   }

   /**
    * {@inheritdoc}
    */
   public function build()
   {
      if ($this->verify()) {
         $this->buildParams($this->infraBuilder->builder());
         return $this;
      }
      return false;
   }

   /**
    * {@inheritdoc}
    */
   public function infraBuilder() : InfraBuilder
   {
      return $this->infraBuilder;
   }

   /**
    * Create the builder for the used infrastructure.
    * @param InfraBuilder $infraBuilder
    * @return InfraBuilder
    */
   protected function createInfraBuilder(InfraBuilder $infraBuilder = null) : InfraBuilder
   {
      if (is_null($infraBuilder)) {
         return new EloquentBuilder($this->entity->query());
      }
      return $infraBuilder;
   }

   /**
    * Verify the base parameters for build the query.
    * @return bool True if the query is valid
    * @throws DomainException In case of error in parameters
    */
   protected function verify() : bool
   {
      if (false === isset($this->queryParams)) {
         return false;
      }
      if (false === $this->queryParams->hasAllFields()) {
         $diff = array_diff($this->queryParams->getArray(QueryParams::FIELD), $this->entity->getVisible());
         if (count($diff) > 0) {
            $mess = 'Unknown field : ' . implode(',', $diff);
            throw new DomainException($mess);
         }
      }
      if ($this->queryParams->has(QueryParams::INCLUDE)) {
         $diff = array_diff($this->queryParams->getArray(QueryParams::INCLUDE), $this->entity->getAssociated());
         if (count($diff) > 0) {
            $mess = 'Unknown object to include : ' . implode(',', $diff);
            throw new DomainException($mess);
         }
      }
      if ($this->queryParams->has(QueryParams::SORT)) {
         $diff = array_diff($this->queryParams->getArray(QueryParams::SORT), $this->entity->getVisible());
         if (count($diff) > 0) {
            $mess = 'Unknown field to sort : ' . implode(',', $diff);
            throw new DomainException($mess);
         }
      }
      if ($this->queryParams->has(QueryParams::DESC)) {
         $diff = array_diff($this->queryParams->getArray(QueryParams::DESC), $this->entity->getVisible());
         if (count($diff) > 0) {
            $mess = 'Unknown field to descendant sort : ' . implode(',', $diff);
            throw new DomainException($mess);
         }
      }
      if ($this->queryParams->hasSearch()) {
         if ($this->queryParams->hasEmptySearch()) {
            throw new DomainException('No field to search');
         }
         $fields = array_keys($this->queryParams->getArray(QueryParams::SEARCH));
         $diff = array_diff($fields, $this->entity->getVisible());
         if (count($diff) > 0) {
            $mess = 'Unknown field to search : ' . implode(',', $diff);
            throw new DomainException($mess);
         }
      }
      return true;
   }

   /**
    * Build the query with the given parameters.
    * @param $query Builder
    */
   protected function buildParams($query)
   {
      if (false === $this->queryParams->hasAllFields()) {
         $query->select($this->queryParams->getArray(QueryParams::FIELD));
      }
      if ($this->queryParams->hasLimit()) {
         $query->skip($this->queryParams->getInt(QueryParams::SKIP));
         $query->limit($this->queryParams->getInt(QueryParams::LIMIT));
      }
      if ($this->queryParams->has(QueryParams::INCLUDE)) {
         $query->with($this->queryParams->getArray(QueryParams::INCLUDE));
      }
      $this->buildParamsSortDesc($query);
      $this->buildParamsSearch($query);
   }

   /**
    * Build the query for Sort and Desc parameters.
    * @param $query Builder
    */
   protected function buildParamsSortDesc($query)
   {
      if ($this->queryParams->has(QueryParams::SORT)) {
         $aSort = $this->queryParams->getArray(QueryParams::SORT);
         $aDesc = $this->queryParams->getArray(QueryParams::DESC);
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
      }
   }

   /**
    * Build the query for Search parameters.
    * @param $query Builder
    */
   protected function buildParamsSearch($query)
   {
      if ($this->queryParams->has(QueryParams::SEARCH)) {
         $aWheres = array();
         $aSearch = $this->queryParams->getArray(QueryParams::SEARCH);
         foreach ($aSearch as $field => $expr) {
            if (is_array($expr)) {
               foreach ($expr as $exprSameField) {
                  $aWheres[] = $this->calculateWhere($field, $exprSameField);
               }
            } else {
               $aWheres[] = $this->calculateWhere($field, $expr);
            }
         }
         $this->addWhere($query, $aWheres);
      }
   }

   /**
    * Add a 'Where' array in the query.
    * @param Builder $pQuery The query
    * @param array $paWheres The array to add
    */
   protected function addWhere($pQuery, $paWheres)
   {
      foreach ($paWheres as $aWhere) {
         if (isset($aWhere['column2'])) {
            $pQuery->whereColumn($aWhere['column'], $aWhere['operator'], $aWhere['column2'], $aWhere['boolean']);
         } elseif (isset($aWhere['date'])) {
            switch ($aWhere['date']) {
               case 'date':
                  $pQuery->whereDate($aWhere['column'], $aWhere['operator'], $aWhere['value'], $aWhere['boolean']);
                  break;
               case 'day':
                  $pQuery->whereDay(
                     $aWhere['column'],
                     $aWhere['operator'],
                     str_pad($aWhere['value'], 2, '0', STR_PAD_LEFT),
                     $aWhere['boolean']
                  );
                  break;
               case 'month':
                  $pQuery->whereMonth(
                     $aWhere['column'],
                     $aWhere['operator'],
                     str_pad($aWhere['value'], 2, '0', STR_PAD_LEFT),
                     $aWhere['boolean']
                  );
                  break;
               case 'year':
                  $pQuery->whereYear($aWhere['column'], $aWhere['operator'], $aWhere['value'], $aWhere['boolean']);
                  break;
               case 'time':
                  $pQuery->whereTime($aWhere['column'], $aWhere['operator'], $aWhere['value'], $aWhere['boolean']);
                  break;
            }
         } else {
            switch ($aWhere['operator']) {
               case '=':
               case '>':
               case '<':
               case '>=':
               case '<=':
               case '<>':
               case 'like':
                  $pQuery->where($aWhere['column'], $aWhere['operator'], $aWhere['value'], $aWhere['boolean']);
                  break;
               case 'null':
                  $pQuery->whereNull($aWhere['column'], $aWhere['boolean'], $aWhere['not']);
                  break;
               case 'in':
                  $pQuery->whereIn($aWhere['column'], $aWhere['value'], $aWhere['boolean'], $aWhere['not']);
                  break;
               case 'between':
                  $pQuery->whereBetween($aWhere['column'], $aWhere['value'], $aWhere['boolean'], $aWhere['not']);
                  break;
            }
         }
      }
   }

   /**
    * Build an 'Where' array with the field name and the filter expression.
    * <p>The returned array can contain :</p>
    * <ul>
    * <li>column : the field name</li>
    * <li>operator : one of operator permitted by SQL Where</li>
    * <li>value : a value to compare, can be 'null'</li>
    * <li>date : a date value to compare</li>
    * <li>boolean : 'and' or 'or' if multiple where</li>
    * <li>not : boolean to flag a negation</li>
    * <li>column2 : an other field name to compare</li>
    * </ul>
    * @param string $pField The field name
    * @param string $pExpr The filter expression
    * @return array|null An 'Where' array or null if the expression is empty
    */
   protected function calculateWhere($pField, $pExpr)
   {
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
      return $aWhere;
   }

   /**
    * Fill an 'Where' array with a piece of expression.
    * @param array $pArray The 'Where' array to fill
    * @param string $pExpr The piece of expression
    * @param string $pIndex The index of the piece
    * @return array The original 'Where' array filled with the piece of expression
    */
   protected function calculateWhereExpression($pArray, $pExpr, $pIndex)
   {
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

   /**
    * Evaluate if the given expression is an operator known by SQL Where.
    * @param string $pExpr The expression
    * @return bool True if it's an operator
    */
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

   /**
    * Evaluate if the given expression is a type date known by SQL Where.
    * @param string $pExpr The expression
    * @return bool True if it's a type date
    */
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

   /**
    * Convert a operator to the enabled by SQL Where.
    * @param string $pExpr The operator expression
    * @return string
    */
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

   /**
    * Extracts the value from the given expression and puts it correctly in her place in the 'Where' array.
    * @param array $pArray The 'Where' array to complete
    * @param string $pExpr The value expression
    * @return array The given array
    */
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

      if (in_array($pExpr, $this->entity->getVisible(), true)) {
         $pArray['column2'] = $pExpr;
      } else {
         $pArray['value'] = $pExpr;
      }
      return $pArray;
   }

}
