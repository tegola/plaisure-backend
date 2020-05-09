<?php

namespace App\Http\Controllers\Site\User\Venues;

use App\Http\Controllers\Controller;
use App\Http\Resources\Venue as VenueResource;

class ListController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth:api');
	}

	/**
	 * Get the venues for the logged in user.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function load()
	{
		$user = auth()->user();
		$venues = $user->venues()
			->with('categories', 'subscriptions')
			->get()
			->each(function($venue) { // Load first photo (limit/take doesn't work with eager loading)
				$venue->load(['photos' => function($query) {
					$query->take(1);
				}]);
			});

		return VenueResource::collection($venues);
	}
}