<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function(){
	// Site
	Route::group(['as' => 'site.'], function(){
		Route::get('',                   ['as' => 'home',        'uses' => 'SiteController@index']);

		Route::get('venues/suggestions', ['as' => 'suggestions', 'uses' => 'SiteController@suggestions']);
		Route::get('venues/explore',     ['as' => 'explore',     'uses' => 'SiteController@explore']);
		Route::get('venues/claim',       ['as' => 'claim',       'uses' => 'SiteController@claim']);
		Route::get('venues/{venue}',     ['as' => 'detail',      'uses' => 'SiteController@detail']); // TODO: /v/nome-sala/hash_per_id

		Route::get('about/company',      ['as' => 'company', 'uses' => 'SiteController@about']);
		Route::get('about/contact',      ['as' => 'contact', 'uses' => 'SiteController@contact']);
	});

	// Admin
	Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin'], function(){
		Route::get('', ['as' => 'home', 'uses' => 'AdminController@index']);

		Route::any('venues/upload',   ['as' => 'venues.upload',   'uses' => 'VenueController@upload']);
		Route::get('venues/maintain', ['as' => 'venues.maintain', 'uses' => 'VenueController@maintain']);
		Route::get('venues/clean',    ['as' => 'venues.clean',    'uses' => 'VenueController@clean']);
	});
});