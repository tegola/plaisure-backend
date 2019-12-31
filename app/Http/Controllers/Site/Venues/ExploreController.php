<?php

namespace App\Http\Controllers\Site\Venues;

use App\Http\Controllers\Controller;
use App\Http\Resources\Venue as VenueResource;
use App\Http\Resources\VenueCategory as VenueCategoryResource;
use App\Models\Venue;
use App\Models\VenueCategory;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
	/**
	 * Load initial explore page data.
	 *
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function data(Request $request)
	{
		$categories = VenueCategory::all();

		return [
			'categories' => VenueCategoryResource::collection($categories)
		];
	}

	/**
	 * Search for venues by given parameters.
	 *
	 * @param  Request
	 * @return \Illuminate\Http\Response
	 */
	public function search(Request $request)
	{
		$country = $request->country;
		$radius = $request->filled('radius') ? intval($request->input('radius')) : 10;
		$c_lat = $request->filled('c_lat') ? floatval($request->input('c_lat')) : null;
		$c_lng = $request->filled('c_lng') ? floatval($request->input('c_lng')) : null;
		$ne_lat = $request->filled('ne_lat') ? floatval($request->input('ne_lat')) : null;
		$ne_lng = $request->filled('ne_lng') ? floatval($request->input('ne_lng')) : null;
		$sw_lat = $request->filled('sw_lat') ? floatval($request->input('sw_lat')) : null;
		$sw_lng = $request->filled('sw_lng') ? floatval($request->input('sw_lng')) : null;

		// Make sure we have all location data
		if (!$c_lat && !$c_lng && !$ne_lat && !$ne_lng && !$sw_lat && !$sw_lng) {
			return response()->json([]);
		}

		// Start loading venues
		$query = Venue::with('categories');

		// Find by bounds or center
		if ($ne_lat && $ne_lng && $sw_lat && $sw_lng) {
			$query->inBounds($ne_lat, $ne_lng, $sw_lat, $sw_lng);

			// If center is also specified, find and order by distance too
			if ($c_lat && $c_lng) {
				$query->withDistanceFrom($c_lat, $c_lng);
			}
		} elseif ($c_lat && $c_lng) {
			$query->withDistanceFrom($c_lat, $c_lng);

			// Limit by radius
			if ($radius) {
				$query
					->having('distance', '<=', $radius)
					->orHaving('distance_with_bonus', '<=', $radius);
			}
		}

		// Filter by category
		if ($request->filled('categories')) {
			$categories = $request->input('categories');
		} else {
			$categories = VenueCategory::forCountry($country)
				->pluck('id')
				->all();
		}

		$categoryIds = array_map('intval', $categories);

		if ($categoryIds) {
			$query->whereHas('categories', function($query) use ($categoryIds) {
				$query->whereIn('id', $categoryIds);
			});
		}

		// Filter by amenities
		/*
		$amenityIds = $request->filled('amenities') ? $request->input('amenities') : [];

		if ($amenityIds) {
			$amenities = $this->amenities();

			foreach ($amenities as $amenity) {
				// Get amenity object
				$currentAmenity = $amenities->where('machine_name', $amenity)->first();

				// Skip if is not a valid amenity
				if (!$currentAmenity) continue;

				$query->orWhere($currentAmenity['field'], true);
			}
		}
		*/

		// Load venues
		// We take a maximum of 100 venues, and the client knows it's the max
		// number it can get. We did it to avoid using simplePaginate(), which
		// is not supported by Fractal transformers, and to avoid paginate(),
		// which don't work with MySQL HAVINGs
		$venues = $query
			->take(100)
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
}
