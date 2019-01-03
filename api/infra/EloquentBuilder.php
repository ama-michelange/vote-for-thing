<?php


namespace Infra;


use Illuminate\Database\Eloquent\Builder;

class EloquentBuilder implements \Domain\InfraBuilder
{
   private $builder;

   /**
    * EloquentBuilder constructor.
    * @param Builder $builder
    */
   public function __construct($builder)
   {
      $this->builder = $builder;
   }

   /**
    * {@inheritdoc}
    */
   public function builder()
   {
      return $this->builder;
   }

}
