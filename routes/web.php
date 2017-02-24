<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['web']], function(){
	// Site
	Route::group(['as' => 'site.'], function(){
		Route::get('',                   ['as' => 'home',               'uses' => 'Site\HomeController@render']);

		Route::get('venues/suggestions', ['as' => 'venues.suggestions', 'uses' => 'Site\SearchController@suggestions']);
		Route::get('venues/explore',     ['as' => 'venues.explore',     'uses' => 'Site\ExploreController@render']);
		Route::get('venues/search',      ['as' => 'venues.search',      'uses' => 'Site\ExploreController@search']);
		Route::get('venues/claim',       ['as' => 'venues.claim',       'uses' => 'Site\ClaimController@render']);
		Route::get('venues/{venue}',     ['as' => 'venues.detail',      'uses' => 'Site\DetailController@render']); // TODO: /v/nome-sala/hash_per_id

		Route::get('about/company',      ['as' => 'about.company',      'uses' => 'Site\AboutController@company']);
		Route::get('about/contact',      ['as' => 'about.contact',      'uses' => 'Site\AboutController@contact']);

		Route::get('user',               ['as' => 'user',               'uses' => 'Site\UserController@index']);

	});

	// Admin
	Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth', 'admin']], function(){
		Route::get('',                ['as' => 'home', 'uses' => 'AdminController@index']);

		Route::any('venues/upload',   ['as' => 'venues.upload',   'uses' => 'VenueController@upload']);
		Route::get('venues/maintain', ['as' => 'venues.maintain', 'uses' => 'VenueController@maintain']);
		Route::post('venues/store',   ['as' => 'venues.store',    'uses' => 'VenueController@store']);
	});
});
 
Auth::routes();
