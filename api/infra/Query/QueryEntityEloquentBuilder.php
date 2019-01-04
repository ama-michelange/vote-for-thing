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

}
