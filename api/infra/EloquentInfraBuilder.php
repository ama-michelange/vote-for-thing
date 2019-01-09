<?php


namespace Infra;


use Domain\InfraBuilder;
use Illuminate\Database\Eloquent\Builder;

class EloquentInfraBuilder implements InfraBuilder
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
