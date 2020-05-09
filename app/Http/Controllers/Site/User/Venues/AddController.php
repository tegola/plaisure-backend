<?php

namespace App\Http\Controllers\Site\User\Venues;

use App\Http\Controllers\Controller;
use App\Http\Resources\Venue as VenueResource;
use App\Http\Resources\VenueCategory as VenueCategoryResource;
use App\Models\Venue;
use App\Models\VenueCategory;
use Illuminate\Http\Request;


class AddController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth:api');

		$this->middleware(function($request, $next) {
			$this->authorize('create', Venue::class);

			return $next($request);
		});
	}

	/**
	 * Load the data for adding a new venue.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function load()
	{
		$categories = VenueCategoryResource::collection(VenueCategory::all());

		return compact('categories');
	}

	/**
	 * Save a new venue.
	 *
	 * @param  Venue $venue
	 * @return \Illuminate\Http\Response
	 */
	public function save(Request $request)
	{
		// Validate
		$this->validate($request, [
			'name'             => 'required|string',
			'address_line1'    => 'required|string',
			'address_line2'    => 'nullable|string',
			'address_city'     => 'required|string',
			'address_postcode' => 'required|string',
			'address_province' => 'required|string',
			'country'          => 'required|string',
			'geo_latitude'     => 'required|numeric|between:-90,90',
			'geo_longitude'    => 'required|numeric|between:-180,180',
			'category_ids'     => 'required|array|exists:venue_categories,id',
			'category_ids'     => 'required|array|exists:venue_categories,id'
		]);

		// Save venue
		$user = auth()->user();

		$venue = new Venue([
			'name' => $request->name,
			'address_line1' => $request->address_line1,
			'address_line2' => $request->address_line2 ?: '',
			'address_city' => $request->address_city,
			'address_postcode' => $request->address_postcode,
			'address_province' => $request->address_province,
			'country' => $request->country,
			'geo_latitude' => $request->geo_latitude,
			'geo_longitude' => $request->geo_longitude,
		]);
		$venue->owner()->associate($user);
		$venue->save();

		// Save categories
		$venue->categories()->sync($request->category_ids);

		return new VenueResource($venue);
	}
}