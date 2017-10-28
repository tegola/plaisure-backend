<?php

namespace App\Http\Middleware;

use Closure;
use App;
use JavaScript;

class PrepareJavascriptData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Javascript::put([
            'app' => [
                'name' => config('app.name')
            ],
            'config' => [
                'locale' => App::getLocale(),
                'csrfToken' => csrf_token(),
                'googleMapsApiKey' => config('constants.google_maps_api_key'),
                'defaultMapCenter' => [
                    // Italy
                    'lat' => 41.909,
                    'lng' => 12.255
                ]
            ]
        ]);

        return $next($request);
    }
}
