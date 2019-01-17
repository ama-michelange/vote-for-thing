<?php

namespace Domain\Entity;

/**
 * Entity Model Category.
 * <ul>Fields :
 * <li>increments('id')</li>
 * <li>string('name', 30)</li>
 * <li>timestamps()</li>
 */
class CategoryEntity extends Entity
{
   protected $table = 'categories';
   
   protected $fillable = [
      'name'
   ];
   
   protected $associated = ['thing'];

   /**
    * Get the things for the category.
    */
   public function things()
   {
      return $this->hasMany('Domain\Entity\ThingEntity', 'category_id');
   }
}
