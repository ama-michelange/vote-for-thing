<?php

namespace App\Entity;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
   use Notifiable, HasRoles, HasApiTokens;

   /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
   protected $dates = [
      'deleted_at',
   ];

   /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
   protected $fillable = [
      'name',
      'email',
      'password',
   ];

   /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
   protected $hidden = [
      'password',
      'remember_token',
   ];

   /**
    * @param array $attributes
    * @return \Illuminate\Database\Eloquent\Model
    */
//   public static function create(array $attributes = [])
//   {
//      if (array_key_exists('password', $attributes)) {
//         $attributes['password'] = bcrypt($attributes['password']);
//      }
//
//      $model = static::query()->create($attributes);
//
//      return $model;
//   }
}
