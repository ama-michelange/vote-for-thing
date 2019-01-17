<?php

namespace Domain\Command;

use Domain\Entity\Entity;
use Illuminate\Support\Collection;
use Infra\Query\QueryEntityEloquentAdapter;

class CommandImp implements Command
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
    * Constructor.
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


}
