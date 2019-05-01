<?php

namespace App\Http\Controllers\Site\Venues;

use Illuminate\Http\Request;

use App\Models\Venue;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Transformers\VenueTransformer;

class DetailController extends Controller
{
	/**
	 * Get the data to show the venue detail page.
	 * 
	 * @param  Venue  $venue
	 * @return Illuminate\Http\Response
	 */
	public function detail(Venue $venue) {
		// Eager load relationships
		$venue->load([
			'businessHours',
			'categories',
			'photos',
			'vltPlatforms'
		]);

		// Get nearby venues (if the plan allows it)
		$nearbyVenues = [];

		if (!$venue->subscription() || !$venue->subscription()->hide_nearby_venues) {
			$nearbyVenues = Venue::near($venue->geo_latitude, $venue->geo_longitude, 5)
				->where('id', '!=', $venue->id)
				->with('categories')
				->take(3)
				->get()
				->transformWith(new VenueTransformer())
				->includeCategories()
				->toArray();
		}

		$venue = fractal($venue, new VenueTransformer())
			->parseIncludes([
				'business_hours',
				'categories',
				'photos',
				'vlt_platforms'
			]);

		return compact('venue', 'nearbyVenues');
	}
}