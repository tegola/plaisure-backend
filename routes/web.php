<?php

use Illuminate\Support\Facades\Route;

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

// Stripe webhooks ------------------------------------------------------------
Route::post('/webhooks/stripe', 'Webhooks\StripeController@handleWebhook');

// SEO ------------------------------------------------------------------------
// FIXME: These still load the 'web' middleware, find a way to remove it
Route::get('/robots.txt',    'SeoController@robots');

// Frontend routes, used only for printing urls easily ------------------------
Route::group([
	'domain' => env('FRONTEND_URL'),
	'prefix' => '/{locale?}'
], function() {
	Route::get('/')                ->name('home');
	Route::get('/login')           ->name('login');
	Route::get('/password/reset')  ->name('password.reset');
	Route::get('/venues/{venue}')  ->name('venues.detail');
	Route::get('/about')           ->name('about');
	Route::get('/promote')         ->name('promote');
	Route::get('/play-responsibly')->name('play-responsibly');
});
