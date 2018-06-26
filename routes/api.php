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

Route::post('/login', 'AuthController@login');
Route::post('/auth/refresh', 'AuthController@refresh');

Route::group(['namespace' => 'Site'], function() {
	Route::get ('/venues/explore/data',   'Venues\ExploreController@data');
	Route::post('/venues/explore/search', 'Venues\ExploreController@search');

	// Venue edit
	Route::group(['middleware' => 'auth:api'], function() {
		Route::get ('/venues/add',            'Venues\FormController@create');
		Route::post('/venues',                'Venues\FormController@store');
		Route::get ('/venues/{venue}',        'Venues\DetailController@detail');
		Route::get ('/venues/{venue}/edit',   'Venues\FormController@edit');
		Route::post('/venues/{venue}',        'Venues\FormController@update');
	});
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});