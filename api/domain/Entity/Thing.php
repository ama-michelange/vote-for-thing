<?php

namespace Domain\Entity;

/**
 * Entity Model Thing.
 * <ul>Fields :
 * <li>increments('id')</li>
 * <li>unsignedInteger('category_id')</li>
 * <li>string('title')</li>
 * <li>string('lib_title')</li>
 * <li>string('proper_title')->nullable()</li>
 * <li>string('number', 20)->nullable()</li>
 * <li>string('image_url')->nullable()</li>
 * <li>string('description_url')->nullable()</li>
 * <li>date('legal')->nullable()</li>
 * <li>text('description')->nullable()</li>
 * <li>timestamps()</li>
 * </ul>
 */
class Thing extends Entity
{
   protected $fillable = [
      'title',
      'lib_title',
      'proper_title',
      'number',
      'image_url',
      'description_url',
      'legal',
      'description'
   ];

   protected $associated = ['category'];

   /**
    * Get the category record associated with the thing.
    */
   public function category()
   {
      return $this->belongsTo('Domain\Entity\Category');
   }

}
