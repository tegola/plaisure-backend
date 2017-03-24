<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Gate;

class Admin
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
        if (Gate::allows('administer')) {
            return $next($request);
        }

        return redirect()->route('site.home');
    }
}
