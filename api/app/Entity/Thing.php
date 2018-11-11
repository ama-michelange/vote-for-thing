<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;


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
 *
 * @package App\Entity
 */
class Thing extends Model
{
   /**
    * Get the category record associated with the thing.
    */
   public function category()
   {
//      return $this->hasOne('App\Entity\Category');
      return $this->belongsTo('App\Entity\Category');
   }
}
