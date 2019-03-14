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

// Route::get('/', function () {
// 	return view('welcome');
// });

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
Route::get('/sitemap.xml',   'SeoController@sitemap');
Route::get('/robots.txt',    'SeoController@robots');

// Frontend routes, used only for printing urls easily ------------------------
Route::group(['domain' => env('FRONTEND_URL')], function() {
	Route::get('/password/reset')->name('password.reset');
});