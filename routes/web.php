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

// Auth
Auth::routes();

// Site
Route::group(['as' => 'site.', 'namespace' => 'Site'], function(){
	Route::get('/',                   'HomeController@index')->name('home');

	Route::get('/venues/suggestions', 'SearchController@suggestions')->name('venues.suggestions');
	Route::get('/venues/explore',     'ExploreController@index')->name('venues.explore');
	Route::get('/venues/search',      'ExploreController@search')->name('venues.search');
	Route::get('/venues/claim',       'ClaimController@index')->name('venues.claim');
	Route::get('/venues/{venue}',     'DetailController@index')->name('venues.detail'); // TODO: /v/nome-sala/hash_per_id

	Route::get('/about/company',      'AboutController@company')->name('about.company');
	Route::get('/about/contact',      'AboutController@contact')->name('about.contact');

	Route::get('/user',               'UserController@index')->name('user');
});

// Admin
Route::group(['prefix' => '/admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth', 'admin']], function(){
	Route::get('/',                'AdminController@index')->name('home');
	Route::any('/venues/upload',   'VenueController@upload')->name('venues.upload');
	Route::get('/venues/maintain', 'VenueController@maintain')->name('venues.maintain');
	Route::post('/venues/store',   'VenueController@store')->name('venues.store');

	Route::match(['get', 'post'], '/venues',              'Venue\ListController@index')->name('venues.index');
	Route::get('/venues/{venue}/edit', 'Venue\FormController@edit') ->name('venues.edit');
});

// SEO
// FIXME: These still load the 'web' middleware, find a way to remove it
Route::get('sitemap', 'SeoController@sitemap') ;
Route::get('robots.txt', 'SeoController@robots') ;