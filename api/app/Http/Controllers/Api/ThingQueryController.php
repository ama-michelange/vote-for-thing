<?php
namespace App\Http\Controllers\Api;

use App\Http\Resources\Resource;
use App\Http\Resources\ThingResource;

class ThingQueryController extends QueryController
{
   /**
    * {@inheritdoc}
    */
   protected function resource() : Resource
   {
      return new ThingResource();
   }
}
