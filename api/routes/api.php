<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(
   [
      'prefix' => 'auth'
   ],
   function () {
      Route::post('login', 'AuthController@login');
      Route::post('signup', 'AuthController@signup');

      Route::group(
         [
            'middleware' => 'auth:api'
         ],
         function () {
            Route::get('logout', 'AuthController@logout');
            Route::get('user', 'AuthController@user');
         }
      );
   }
);

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//   return $request->user();
//});
//
//Route::group(['middleware' => 'auth:api'], function () {
//   Route::post('things', 'ThingController@store')->name('vft.things.create');
//   Route::put('things/{thing}', 'ThingController@update')->name('vft.things.update');
//   Route::delete('things/{thing}', 'ThingController@destroy')->name('vft.things.delete');
//});

//Route::group(['middleware' => 'api'], function () {
//   Route::get('things', 'ThingController@index')->name('vft.things.all');
//   Route::get('things/search', 'ThingController@search')->name('vft.things.search');
//   Route::get('things/{thing}', 'ThingController@show')->name('vft.things.one');
//});

//$api = app('Dingo\Api\Routing\Router');
DingoRoute::version('v1', function () {
   DingoRoute::get('things', 'App\Http\Controllers\ThingController@index')->name('vft.things.all');
   DingoRoute::get('things/search', 'App\Http\Controllers\ThingController@search')->name('vft.things.search');
   DingoRoute::get('things/{thing}', 'App\Http\Controllers\ThingController@show')->name('vft.things.id');

   DingoRoute::get('qthings', 'App\Http\Controllers\Api\ThingEntityController@index')->name('v4t.things');
   DingoRoute::get('qthings/search', 'App\Http\Controllers\Api\ThingEntityController@search')->name('v4t.things.search');
   DingoRoute::get('qthings/{thing}', 'App\Http\Controllers\Api\ThingEntityController@show')->name('v4t.things.id');
   DingoRoute::post('qthings', 'App\Http\Controllers\Api\ThingEntityController@store')->name('v4t.things.create');

   DingoRoute::group(['middleware' => ['throttle:60,1', 'bindings'], 'namespace' => 'App\Http\Controllers'], function ($api) {

      DingoRoute::get('ping', 'Api\PingController@index')->name('ping');;

//      DingoRoute::get('assets/{uuid}/render', 'Api\Assets\RenderFileController@show');

//      DingoRoute::group(['middleware' => ['auth:api'], ], function ($api) {
//
//         DingoRoute::group(['prefix' => 'users'], function ($api) {
//            DingoRoute::get('/', 'Api\Users\UsersController@index');
//            DingoRoute::post('/', 'Api\Users\UsersController@store');
//            DingoRoute::get('/{uuid}', 'Api\Users\UsersController@show');
//            DingoRoute::put('/{uuid}', 'Api\Users\UsersController@update');
//            DingoRoute::patch('/{uuid}', 'Api\Users\UsersController@update');
//            DingoRoute::delete('/{uuid}', 'Api\Users\UsersController@destroy');
//         });
//
//         DingoRoute::group(['prefix' => 'roles'], function ($api) {
//            DingoRoute::get('/', 'Api\Users\RolesController@index');
//            DingoRoute::post('/', 'Api\Users\RolesController@store');
//            DingoRoute::get('/{uuid}', 'Api\Users\RolesController@show');
//            DingoRoute::put('/{uuid}', 'Api\Users\RolesController@update');
//            DingoRoute::patch('/{uuid}', 'Api\Users\RolesController@update');
//            DingoRoute::delete('/{uuid}', 'Api\Users\RolesController@destroy');
//         });
//
//         DingoRoute::get('permissions', 'Api\Users\PermissionsController@index');
//
//         DingoRoute::group(['prefix' => 'me'], function($api) {
//            DingoRoute::get('/', 'Api\Users\ProfileController@index');
//            DingoRoute::put('/', 'Api\Users\ProfileController@update');
//            DingoRoute::patch('/', 'Api\Users\ProfileController@update');
//            DingoRoute::put('/password', 'Api\Users\ProfileController@updatePassword');
//         });
//
//         DingoRoute::group(['prefix' => 'assets'], function($api) {
//            DingoRoute::post('/', 'Api\Assets\UploadFileController@store');
//         });
//
//      });

   });
});
