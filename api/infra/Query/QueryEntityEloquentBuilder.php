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
    */
   public function __construct(Entity $entity)
   {
      $this->entity = $entity;
      $this->createInfraBuilder();
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
   public function build() : void
   {
      $this->verify();
      $this->buildParameters($this->infraBuilder->builder());
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
    * @return \Domain\InfraBuilder
    */
   protected function createInfraBuilder() : InfraBuilder
   {
      $this->infraBuilder = new EloquentBuilder($this->entity->query());
      return $this->infraBuilder;
   }

   /**
    * Verify the base parameters for build the query.
    * @throws DomainException In case of error in parameters
    */
   protected function verify()
   {
      if ($this->queryParams) {
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
      }
   }

   /**
    * Build the query with the given parameters.
    * @param $query Builder
    */
   protected function buildParameters($query)
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
