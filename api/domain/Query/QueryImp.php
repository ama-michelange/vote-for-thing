<?php

namespace Domain\Query;

use Domain\Entity\Entity;
use Illuminate\Support\Collection;
use Infra\Query\QueryEntityEloquentAdapter;

class QueryImp implements Query
{
   /**
    * Entity domain instance.
    *
    * @var Entity;
    */
   protected $entity;

   /**
    * Adapter to the infra query instance.
    *
    * @var Query;
    */
   protected $adapter;

   /**
    * QueryEntityImp constructor.
    * @param Entity $entity
    */
   public function __construct(Entity $entity)
   {
      $this->entity = $entity;
      $this->adapter = new QueryEntityEloquentAdapter($this->entity);
   }

   /**
    * {@inheritdoc}
    */
   public function entity()
   {
      return $this->entity;
   }

   /**
    * {@inheritdoc}
    */
   public function builder() : QueryEntityBuilder
   {
      return $this->adapter->builder();
   }

   /**
    * {@inheritdoc}
    */
   public function findCollection(QueryParams $queryParams) : Collection
   {
      // TODO Action supplémentaire à faire ici
//      $ori = $this->builder()->infraBuilder()->getBuilder();
//      if (isset($ori)) {
//         Log::debug('Infra Builder exist');
//         $ori->select('id');
//      }
      return $this->adapter->findCollection($queryParams);
   }
}
