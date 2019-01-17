<?php

namespace Domain\Entity;

/**
 * Entity Model Group.
 * <ul>Fields :
 * <li>increments('id')</li>
 * <li>string('name', 30)</li>
 * <li>timestamps()</li>
 */
class GroupEntity extends Entity
{
   protected $table = 'groups';
   
   protected $fillable = [
      'name'
   ];
}
