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
	public function detail($venueId)
	{
		// Find venue either normal or deleted
		$venue = Venue::withTrashed()
			->where('id_hashed', $venueId)
			->firstOrFail();

		if (!$venue->trashed()) {

			// Venue is still present, return it along with reviews, the user
			// review and nearby venues

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
			if (!$venue->subscription() || !$venue->subscription()->hide_nearby_venues) {
				$nearbyVenues = $this->getNearby($venue);
			} else {
				$nearbyVenues = [];
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

		} else {

			// Venue was deleted, return 404 with the nearby venues
			return response([
				'nearbyVenues' => $this->getNearby($venue)
			], 404);

		}
	}

	/**
	 * Get venues nearby the specified one within 5km of radius.
	 *
	 * @param  Venue $venue
	 * @return [Venue]
	 */
	private function getNearby(Venue $venue)
	{
		return Venue::near($venue->geo_latitude, $venue->geo_longitude, 5)
			->where('id', '!=', $venue->id)
			->with('categories')
			->take(4)
			->get()
			->transformWith(new VenueTransformer())
			->includeCategories()
			->toArray();
	}
}