<?php

namespace App\Providers;

use Javascript;
use Illuminate\Support\ServiceProvider;
use Schema;
use Blade;
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

        Javascript::put([
            'app' => [
                'name' => config('app.name')
            ],
            'config' => [
                'locale' => App::getLocale(),
                'googleMapsApiKey' => config('constants.google_maps_api_key'),
                'defaultMapCenter' => [
                    // Italy
                    'lat' => 41.909,
                    'lng' => 12.255
                ],
                'plans' => config('plans') // FIXME: Show only on the admin section
            ]
        ]);
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
