<?php

namespace Infra\Query;

use Domain\Entity\Entity;
use Domain\Query\QueryEntity;
use Domain\Query\QueryEntityBuilder;
use Domain\Query\QueryParams;
use Illuminate\Support\Collection;

class QueryEntityEloquentAdapter implements QueryEntity
{

   /**
    * Entity domain instance.
    *
    * @var Entity;
    */
   protected $entity;

   /**
    * The builder instance.
    *
    * @var QueryEntityBuilder;
    */
   protected $builder;

   /**
    * Cconstructor.
    * @param Entity $entity
    */
   public function __construct(Entity $entity)
   {
      $this->entity = $entity;
      $this->builder = new QueryEntityEloquentBuilder($this->entity);
   }

   /**
    * {@inheritdoc}
    */
   public function entity()
   {
      $this->entity;
   }

   /**
    * {@inheritdoc}
    */
   public function builder() : QueryEntityBuilder
   {
      return $this->builder;
   }

   /**
    * {@inheritdoc}
    */
   public function findCollection(QueryParams $queryParams) : Collection
   {
      $this->builder->withParams($queryParams)->build();
      return $this->builder->infraBuilder()->builder()->get();
   }

   /**
    * {@inheritdoc}
    */
   public function findItem($id, QueryParams $queryParams) : Entity
   {
      $ret = null;
      $this->builder->withParams($queryParams)->build();
      if ($queryParams->hasUseAsId()) {
         $ret = $this->builder->infraBuilder()->builder()->first();
      } else {
         $ret = $this->builder->infraBuilder()->builder()->find($id);
      }
      return $ret;
   }
}
