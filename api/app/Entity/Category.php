<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * Entity Model Category.
 * <ul>Fields :
 * <li>increments('id')</li>
 * <li>string('name', 30)</li>
 * <li>timestamps()</li>
 *
 * @package App\Entity
 */
class Category extends Model
{
   /**
    * Get the things for the category.
    */
   public function things()
   {
      //      return $this->hasMany(Thing::class);
      return $this->hasMany('App\Entity\Thing');
   }
}
