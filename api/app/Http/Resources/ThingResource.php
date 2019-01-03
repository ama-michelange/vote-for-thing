<?php

namespace App\Http\Resources;


use App\Http\Transformers\ThingTransformer;
use Domain\Entity\Thing;

class ThingResource extends Resource
{

   /**
    * {@inheritdoc}
    */
   protected function transformer()
   {
      return new ThingTransformer();
   }

   /**
    * {@inheritdoc}
    */
   public function entity()
   {
      return new Thing();
   }
}
