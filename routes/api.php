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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
// 	return $request->user();
// });

// File upload and view/download
Route::post('/files',                      'FileController@upload');
Route::get('/files/{file}/{size}/{token}', 'FileController@view')->name('files.show');

// Authorization
Route::post('/auth/register',        'AuthController@register');
Route::post('/auth/login',           'AuthController@login');
// Route::post('/auth/refresh',         'AuthController@refresh');
Route::post('/auth/logout',          'AuthController@logout');
Route::post('/auth/password/forgot', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/auth/password/reset',  'Auth\ResetPasswordController@reset');

// User data
// FIXME: Move mostly in the Site portion
Route::get ('/user',          'UserController@user');
Route::get('/user/venues',    'UserController@venues');
Route::get('/user/edit',      'UserController@edit');
Route::post('/user/info',     'UserController@info');
Route::post('/user/billing',  'UserController@billing');
Route::post('/user/password', 'UserController@password');

Route::group([
	'namespace' => 'Site',
	'middleware' => 'throttle:60,1'
], function() {
	// Home page
	Route::get('/home', 'HomeController@data'); // Had issues with '/'

	// User
	Route::get('/user/favorites',         'User\FavoritesController@load');
	Route::post('/user/favorites/add',    'User\FavoritesController@add');
	Route::post('/user/favorites/remove', 'User\FavoritesController@remove');
	
	// Explore
	Route::get ('/venues/explore', 'Venues\ExploreController@data');
	Route::post('/venues/explore', 'Venues\ExploreController@search');

	// Venue add/edit
	Route::get ('/venues/add',          'Venues\FormController@create');
	Route::post('/venues',              'Venues\FormController@store');
	Route::get ('/venues/{venue}/edit', 'Venues\FormController@edit');
	Route::post('/venues/{venue}',      'Venues\FormController@update');

	// Venue subscription
	Route::post('/venues/{venue}/subscribe', 'Venues\SubscriptionController@update');

	// Venue claim
	Route::get ('/venues/{venue}/claim',  'Venues\ClaimController@load');
	Route::post('/venues/{venue}/claim',  'Venues\ClaimController@confirm');

	// Venue detail
	Route::get ('/venues/{venue}',        'Venues\DetailController@detail');

	// Venue detail -> reviews
	Route::get('/venues/{venue}/reviews',                  'Venues\ReviewController@index');
	Route::post('/venues/{venue}/reviews',                 'Venues\ReviewController@store');
	Route::post('/venues/{venue}/reviews/{review}/reply',  'Venues\ReviewController@reply');
	Route::post('/venues/{venue}/reviews/{review}/report', 'Venues\ReviewController@report');
});

Route::group([
	'prefix' => '/admin',
	'namespace' => 'Admin',
	'middleware' => ['auth:api', 'can:administer'],
], function() {
	// Route::get('/', 'AdminController@index')->name('admin.home');

	// Venues
	Route::get('/venues',              'Venues\ListController@index');
	Route::get('/venues/{venue}',      'Venues\DetailController@detail');
	Route::get('/venues/add',          'Venues\FormController@load');
	Route::post('/venues',             'Venues\FormController@save');
	Route::get('/venues/{venue}/edit', 'Venues\FormController@load');
	Route::put('/venues/{venue}',      'Venues\FormController@save');
	Route::delete('/venues/{venue}',   'Venues\ListController@delete');

	// Venue imports
	Route::get('/venue-imports',               'VenueImports\ListController@index');
	Route::get('/venue-imports/{venueImport}', 'VenueImports\DetailController@detail');

	// Users
	Route::get('/users',        'Users\ListController@index');
	Route::get('/users/{user}', 'Users\DetailController@detail');

	// Venues
	/*
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
	*/
});