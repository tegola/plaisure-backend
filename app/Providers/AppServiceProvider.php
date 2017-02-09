<?php

namespace App\Providers;

use Javascript;
use Illuminate\Support\ServiceProvider;

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
            'constants' => [
                'googleMapsApiKey' => config('constants.google_maps_api_key')
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
