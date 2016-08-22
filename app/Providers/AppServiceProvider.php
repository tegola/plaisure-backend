<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Force SSL on production (we have an SSL certificate there)
        // http://www.jeffmould.com/2016/01/31/laravel-5-2-forcing-https-routes-when-using-ssl/
        if (!App::isLocal()) {
            URL::forceSchema('https');
        }
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
