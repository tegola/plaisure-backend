<?php

namespace App\Providers;

use Javascript;
use Illuminate\Support\ServiceProvider;
use Schema;
use Blade;
use Carbon;
use App;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Support utf8mb4 in MySQL <5.7.7
		// https://laravel.com/docs/master/migrations#creating-indexes
		Schema::defaultStringLength(191);

		// Default nl2br in blade echo tags
		Blade::setEchoFormat('nl2br(e(%s))');

		// Set locale for dates
		Carbon::setLocale(App::getLocale());
		setlocale(LC_TIME, App::getLocale());
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
