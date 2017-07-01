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

// Auth -----------------------------------------------------------------------
Auth::routes();

// Site -----------------------------------------------------------------------
Route::group(['namespace' => 'Site'], function(){
	Route::get('/',                  'HomeController@index')      ->name('site.home');
	Route::post('/suggestions',      'HomeController@suggestions')->name('site.suggestions');

	Route::group(['prefix' => '/venues', 'namespace' => 'Venue'], function(){
		Route::get('/explore',       'ExploreController@index')   ->name('site.venues.explore');
		Route::get('/search',        'ExploreController@search')  ->name('site.venues.search');
		Route::get('/{venue}',       'DetailController@index')    ->name('site.venues.detail'); // TODO: /v/nome-sala/hash_per_id
		Route::get('/{venue}/claim', 'ClaimController@index')     ->name('site.venues.claim');
	});

	Route::get('/about/company',     'AboutController@company')   ->name('site.about.company');
	Route::get('/about/contact',     'AboutController@contact')   ->name('site.about.contact');
    
	Route::get('/user',              'UserController@index')      ->name('site.user');
});

// Admin ----------------------------------------------------------------------
Route::group(['prefix' => '/admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'admin']], function(){
	Route::get('/', 'AdminController@index')->name('admin.home');

	// Venues
	Route::group(['prefix' => '/venues', 'namespace' => 'Venue'], function(){
		// Obsolete
		Route::group(['prefix' => '/obsolete', 'namespace' => 'Obsolete'], function(){
			Route::get('/', 'ListController@index')->name('admin.venues.obsolete.index');
		});

		// Unmanaged
		Route::group(['prefix' => '/unmanaged', 'namespace' => 'Unmanaged'], function(){
			Route::get('/',                        'ListController@index')  ->name('admin.venues.unmanaged.index');
			Route::get('/{importedVenue}/promote', 'FormController@promote')->name('admin.venues.unmanaged.promote');
		});

		// Import (upload CSV)
		Route::group(['namespace' => 'Import'], function(){
			Route::get('/import', 'FormController@edit')   ->name('admin.venues.import.edit');
			Route::post('/import', 'FormController@update')->name('admin.venues.import.update');
		});

		// Normal ones
		Route::get('/',             'ListController@index')         ->name('admin.venues.index');
		Route::get('/add',          'FormController@create')        ->name('admin.venues.create');
		Route::post('/',            'FormController@store')         ->name('admin.venues.store');
		// Route::get('/{venue}',      'DetailController@show')        ->name('admin.venues.show');
		Route::get('/{venue}/edit', 'FormController@edit')          ->name('admin.venues.edit');
		Route::patch('/{venue}',    'FormController@update')        ->name('admin.venues.update');
		Route::delete('/{venue}',   'ListController@delete')        ->name('admin.venues.delete');

	});

	// Users
	Route::get('/users', 'User\ListController@index')->name('admin.users.index');
});

// SEO ------------------------------------------------------------------------
// FIXME: These still load the 'web' middleware, find a way to remove it
Route::get('sitemap', 'SeoController@sitemap') ;
Route::get('robots.txt', 'SeoController@robots') ;