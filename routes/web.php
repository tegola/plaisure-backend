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
// Auth::routes();

// Send mail test -------------------------------------------------------------
Route::get('/send-mail', function() {
	$u = App\Models\User::find(1);
	$u->sendPasswordResetNotification('pippo');

	return 'OK';
});

// Stripe webhooks ------------------------------------------------------------
Route::post('/stripe/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');

// SEO ------------------------------------------------------------------------
// FIXME: These still load the 'web' middleware, find a way to remove it
Route::get ('/venues/{id}',  'SeoController@redirectToHashed')->where('id', '[0-9]{1,9}+'); // FIXME: Remove whene there are no more hits
Route::get('/sitemap',       'SeoController@sitemap')->name('sitemap');
Route::get('/robots.txt',    'SeoController@robots') ;

// Single page app ------------------------------------------------------------
Route::get('/{any}', 'Site\MainController@index')->where('any', '.*');