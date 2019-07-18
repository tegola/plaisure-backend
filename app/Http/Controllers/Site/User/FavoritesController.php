<?php

namespace App\Http\Controllers\Site\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transformers\VenueTransformer;
use Hashids\Hashids;
use App\Models\Venue;

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
		$venues = auth()->user()->favorites
			// FIXME: Migliorare la query
			->each(function($venue) { // Load only first photo
				$venue->load([
					'photos' => function($query) {
						$query->take(1);
					}
				]);
			})
			->transformWith(new VenueTransformer())
			->parseIncludes([
				'categories',
				'photos',
				'subscription'
			]);

		return compact('venues');
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