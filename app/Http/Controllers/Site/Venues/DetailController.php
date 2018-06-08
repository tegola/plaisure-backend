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
	 * Show the venue detail page.
	 * 
	 * @param  Venue  $venue
	 * @return Illuminate\Http\Response
	 */
	public function index(Venue $venue) {
		// Eager load relationships
		$venue->load([
			'businessHours',
			'categories',
			'payPerViewPlatforms',
			'photos',
			'vltPlatforms'
		]);

		// Get nearby venues (if the plan allows it)
		$nearbyVenues = null;

		if (!$venue->plan || !$venue->plan->hide_nearby_venues) {
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
				'pay_per_view_platforms',
				'photos',
				'vlt_platforms'
			]);

		return compact('venue', 'nearbyVenues');
	}
}