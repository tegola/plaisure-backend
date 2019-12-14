<?php

namespace App\Http\Controllers\Site\Venues;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venue;
use App\Transformers\VenueTransformer;

use App\Http\Resources\Review as ReviewResource;

class DetailController extends Controller
{
	/**
	 * Get the data to show the venue detail page.
	 * 
	 * @param  Venue  $venue
	 * @return \Illuminate\Http\Response
	 */
	public function detail(Venue $venue) {
		// Eager load relationships
		$venue->load([
			'businessHours',
			'amenities',
			'categories',
			'photos',
			'vltPlatforms',
			'reviews' => function($query) {
				return $query->latest()->take(2);
			},
			'reviews.user'
		]);

		// Get the review for the current user
		$user = auth()->guard('api')->user(); // Guard needed since we don't have the auth:api middleware set here

		if ($user) {
			$userReview = $venue->reviews->where('user_id', $user->id)->first();
			if ($userReview) $userReview = new ReviewResource($userReview);
		}

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
				'amenities',
				'categories',
				'photos',
				'vlt_platforms',
				'reviews'
			]);

		return compact('venue', 'userReview', 'nearbyVenues');
	}
}