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
		$cLat = $request->filled('c_lat') ? floatval($request->input('c_lat')) : null;
		$cLng = $request->filled('c_lng') ? floatval($request->input('c_lng')) : null;
		$neLat = $request->filled('ne_lat') ? floatval($request->input('ne_lat')) : null;
		$neLng = $request->filled('ne_lng') ? floatval($request->input('ne_lng')) : null;
		$swLat = $request->filled('sw_lat') ? floatval($request->input('sw_lat')) : null;
		$swLng = $request->filled('sw_lng') ? floatval($request->input('sw_lng')) : null;
		$inBounds = ($neLat && $neLng && $swLat && $swLng);

		// Make sure we have all location data
		if (!$cLat && !$cLng && !$neLat && !$neLng && !$swLat && !$swLng) {
			return response()->json([]);
		}

		// Start loading venues
		$query = Venue::with('categories');

		// Find by bounds or center
		if ($inBounds) {
			$query->inBounds($neLat, $neLng, $swLat, $swLng);

			// If center is also specified, find and order by distance too
			if ($cLat && $cLng) {
				$query->withDistanceFrom($cLat, $cLng);
			}
		} else if ($cLat && $cLng) {
			$query->withDistanceFrom($cLat, $cLng);

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

		// Paginate venues ($page is inferred automatically)
		$venues = $query->paginate($inBounds ? 200 : 20);

		// Load first photo (limit/take doesn't work with eager loading)
		$venues->getCollection()->each(function($venue) {
			$venue->load(['photos' => function($query) {
				$query->take(1);
			}]);
		});

		return VenueResource::collection($venues);
	}
}
