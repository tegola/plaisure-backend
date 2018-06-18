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

// App client API -------------------------------------------------------------
Route::group(['prefix' => '/support', 'namespace' => 'Site'], function() {
	Route::post('/suggestions',           'HomeController@suggestions');

	Route::get ('/venues/explore/data',   'Venues\ExploreController@data');
	Route::post('/venues/explore/search', 'Venues\ExploreController@search');

	Route::get ('/venues/add',            'Venues\FormController@create');
	Route::post('/venues',                'Venues\FormController@store');
	Route::get ('/venues/{venue}',        'Venues\DetailController@detail');
	Route::get ('/venues/{venue}/edit',   'Venues\FormController@edit');
	Route::post('/venues/{venue}',        'Venues\FormController@update');
});

// Site -----------------------------------------------------------------------
Route::group(['namespace' => 'Site'], function(){
	// Route::get('/',             'HomeController@index')      ->name('site.home');
    
    // User
	Route::group(['middleware' => 'auth'], function() {
		Route::get('/user', 'UserController@index')->name('site.user');
	});
});

// Admin ----------------------------------------------------------------------
Route::group(['prefix' => '/admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'admin']], function(){
	Route::get('/', 'AdminController@index')->name('admin.home');

	// Venues
	Route::group(['prefix' => '/venues', 'namespace' => 'Venues'], function(){
		// Obsolete
		Route::get('/obsolete', 'ObsoleteListController@index')->name('admin.venues.obsolete.index');

		// Unmanaged
		Route::get('/unmanaged', 'UnmanagedListController@index')->name('admin.venues.unmanaged.index');

		// Import (upload CSV)
		Route::get('/import',  'ImportFormController@edit')  ->name('admin.venues.import.edit');
		Route::post('/import', 'ImportFormController@update')->name('admin.venues.import.update');

		// Normal ones
		Route::get('/',                        'ListController@index')  ->name('admin.venues.index');
		Route::get('/add',                     'FormController@create') ->name('admin.venues.create');
		Route::get('/promote/{importedVenue}', 'FormController@promote')->name('admin.venues.promote');
		Route::post('/',                       'FormController@store')  ->name('admin.venues.store');
		// Route::get('/{venue}',                 'DetailController@show') ->name('admin.venues.show');
		Route::get('/{venue}/edit',            'FormController@edit')   ->name('admin.venues.edit');
		Route::patch('/{venue}',               'FormController@update') ->name('admin.venues.update');
		Route::delete('/{venue}',              'ListController@delete') ->name('admin.venues.delete');

	});

	// Users
	Route::get('/users', 'Users\ListController@index')->name('admin.users.index');
});

// File upload/download/view --------------------------------------------------
Route::group(['prefix' => '/files', 'middleware' => ['auth']], function() {
	Route::post('/',                     'FileController@upload')->name('files.store');
	Route::get('/{file}/{size}/{token}', 'FileController@view')  ->name('files.show');
});

// SEO ------------------------------------------------------------------------
// FIXME: These still load the 'web' middleware, find a way to remove it
Route::get ('/venues/{id}',  'SeoController@redirectToHashed')->where('id', '[0-9]{1,9}+'); // FIXME: Remove whene there are no more hits
Route::get('/sitemap',       'SeoController@sitemap')->name('sitemap');
Route::get('/robots.txt',    'SeoController@robots') ;

// Single page app ------------------------------------------------------------
Route::get('/{any}', 'Site\MainController@index')->where('any', '.*');