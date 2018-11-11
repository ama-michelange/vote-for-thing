<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
   return $request->user();
});

//Route::group(['middleware' => 'auth:api'], function () {
//	Route::post('things', 'ThingController@store');
//	Route::put('things/{thing}', 'ThingController@update');
//	Route::delete('things/{thing}', 'ThingController@destroy');
//});

Route::group(['middleware' => 'api'], function () {
   Route::get('things', 'ThingController@index');
   Route::get('things/search', 'ThingController@search');
   Route::get('things/{thing}', 'ThingController@show');
});
