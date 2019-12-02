<?php

namespace App\Http\Controllers\Site\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Venue as VenueResource;
use App\Models\Venue;
use Illuminate\Http\Request;

class FavoritesController extends Controller
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
	 * Load the user's list of favorite venues.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function load(Request $request)
	{
		$user = auth()->user();
		$venues = $user->favorites()
			->with('categories')
			->get()
			->each(function($venue) { // Load first photo (limit/take doesn't work with eager loading)
				$venue->load(['photos' => function($query) {
					$query->take(1);
				}]);
			});

		return [
			'venues' => VenueResource::collection($venues)
		];
	}

	/**
	 * Add a favorite.
	 * 
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function add(Request $request)
	{
		$user = auth()->user();

		if ($request->has('id')) {
			$venueId = Venue::decodeHashedId($request->id);

			$user->favorites()->detach($venueId); // Avoid duplicates
			$user->favorites()->attach($venueId);
		}
	}

	/**
	 * Remove a favorite.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function remove(Request $request)
	{
		$user = auth()->user();

		if ($request->has('id')) {
			$venueId = Venue::decodeHashedId($request->id);

			$user->favorites()->detach($venueId);
		}
	}
}