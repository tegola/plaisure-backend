<?php

namespace App\Providers;

use Javascript;
use Illuminate\Support\ServiceProvider;
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
        //
        Javascript::put([
            'config' => [
                'locale' => App::getLocale(),
                'googleMapsApiKey' => config('constants.google_maps_api_key'),
                'defaultMapCenter' => [
                    'lat' => 41.2053112,
                    'lng' => 8.0860841
                ]
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
