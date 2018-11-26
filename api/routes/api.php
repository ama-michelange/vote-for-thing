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

Route::group([
   'prefix' => 'auth'
], function () {
   Route::post('login', 'AuthController@login');
   Route::post('signup', 'AuthController@signup');

   Route::group([
      'middleware' => 'auth:api'
   ], function () {
      Route::get('logout', 'AuthController@logout');
      Route::get('user', 'AuthController@user');
   });
});

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//   return $request->user();
//});
//
//Route::group(['middleware' => 'auth:api'], function () {
//   Route::post('things', 'ThingController@store')->name('vft.things.create');
//   Route::put('things/{thing}', 'ThingController@update')->name('vft.things.update');
//   Route::delete('things/{thing}', 'ThingController@destroy')->name('vft.things.delete');
//});

Route::group(['middleware' => 'api'], function () {
   Route::get('things', 'ThingController@index')->name('vft.things.all');
   Route::get('things/search', 'ThingController@search')->name('vft.things.search');
   Route::get('things/{thing}', 'ThingController@show')->name('vft.things.one');
});
