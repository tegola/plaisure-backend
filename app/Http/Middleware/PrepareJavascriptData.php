<?php

namespace App\Http\Middleware;

use Closure;
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
			'csrfToken' => csrf_token()
		]);

		return $next($request);
	}
}
