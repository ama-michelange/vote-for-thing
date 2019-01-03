<?php

namespace Domain\Entity;

/**
 * Entity Model Category.
 * <ul>Fields :
 * <li>increments('id')</li>
 * <li>string('name', 30)</li>
 * <li>timestamps()</li>
 */
class Category extends Entity
{
   protected $fillable = [
      'name'
   ];
   
   /**
    * Get the things for the category.
    */
   public function things()
   {
      return $this->hasMany('Domain\Entity\Thing');
   }
}
