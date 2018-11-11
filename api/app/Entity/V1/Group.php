<?php

namespace App\Entity\V1;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
   /**
    * The connection associated with the model.
    *
    * @var string
    */
   protected $connection = 'mysql_v1';
   /**
    * The table associated with the model.
    *
    * @var string
    */
   protected $table = 'vfa_groups';
   /**
    * The primary key associated with the model.
    *
    * @var string
    */
   protected $primaryKey = 'group_id';
}
