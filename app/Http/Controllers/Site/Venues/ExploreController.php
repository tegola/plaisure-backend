<?php

namespace App\Http\Controllers\Site\Venues;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\VenueCategory;
use App\Transformers\VenueTransformer;
use App;

class ExploreController extends Controller
{
	/*
	private function amenities() {
		return collect([
			['field' => 'amenity_atm',             'machine_name' => 'atm'],
			['field' => 'amenity_bar',             'machine_name' => 'bar'],
			['field' => 'amenity_pay_per_view',    'machine_name' => 'pay_per_view'],
			['field' => 'amenity_pos',             'machine_name' => 'pos'],
			['field' => 'amenity_private_parking', 'machine_name' => 'private_parking'],
			['field' => 'amenity_restaurant',      'machine_name' => 'restaurant'],
			['field' => 'amenity_security',        'machine_name' => 'security'],
			['field' => 'amenity_smoking_area',    'machine_name' => 'smoking_area'],
			['field' => 'amenity_wifi',            'machine_name' => 'wifi']
		]);
	}
	*/

	/**
	 * Load initial explore page data.
	 *
	 * @param  Request $request
	 * @return Illuminate\Http\Response
	 */
	public function data(Request $request)
	{
		$country = $this->extractCountry($request);

		$categories = VenueCategory::forCountry($country)
			->select('id', 'machine_name')
			->get();
		// $amenities = $this->amenities()->all();

		return compact('categories'/*, 'amenities'*/);
	}

	/**
	 * Search for venues by given parameters.
	 *
	 * @param  Request
	 * @return Illuminate\Http\Response
	 */
	public function search(Request $request)
	{
		$country = $this->extractCountry($request);

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

		// Load venues with photos
		// We take a maximum of 100 venues, and the client knows it's the max
		// number it can get. We did it to avoid using simplePaginate(), which
		// is not supported by Fractal transformers, and to avoid paginate(),
		// which don't work with MySQL HAVINGs
		$venues = $query
			->take(100)
			->get()
			->each(function($venue) {
				$venue->load([
					'photos' => function($query) {
						$query->take(1);
					}
				]);
			});

		// Return results
		$venues = $venues
			->transformWith(new VenueTransformer())
			->parseIncludes([
				'categories',
				'photos'
			]);

		return $venues;
	}

	/**
	 * Prepare category and venue suggestions, the latter only if location data
	 * is present.
	 *
	 * @param  Request $request
	 * @return array
	 */
	/*
	public function suggestions(Request $request)
	{
		$query = trim($request->input('query'));
		$venues = [];
		$categories = [];
		$suggestions = [];

		// Find venues and categories
		if ($query) {
			$tokens = explode(' ', $query);

			// Venues
			$venuesQuery = Venue::with('categories');

			// Find in venue name
			foreach ($tokens as $token) {
				$venuesQuery->where('name', 'like', "%{$token}%");
			}

			// Find in categories name
			$venuesQuery->orWhereHas('categories', function($query) use ($tokens) {
				foreach ($tokens as $token) {
					$query->where('name', 'like', "%{$token}%");
				}
			});

			$venues = $venuesQuery
				->latest()
				->take(5)
				->get();

			// Categories
			$categoriesQuery = VenueCategory::query();
			foreach ($tokens as $token) {
				$categoriesQuery->orWhere('name', 'like', "%{$token}%");
			}
			$categories = $categoriesQuery->take(5)->get();
		} else {
			// Just categories
			$categories = VenueCategory::take(5)->get();
		}

		// Prepare suggestions (categories first)
		foreach ($categories as $c) {
			array_push($suggestions, [
				'type' => 'category',
				'id' => $c->id,
				'name' => $c->name
			]);
		}
		foreach ($venues as $v) {
			array_push($suggestions, [
				'type' => 'venue',
				'id' => $v->id,
				'name' => $v->name,
				'category' => $v->categories()->first()->name,
				'city' => $v->address_city,
				'url' => "/venues/{$v->id_hashed}"
			]);
		}

		return $suggestions;
	}
	*/

	/**
	 * Find the country for the user, or use a default.
	 *
	 * @param  Request $request
	 * @return string
	 */
	private function extractCountry(Request $request)
	{
		$user = auth()->user();
		$country = $user ? locale_get_region($user->locale) : $request->input('country', 'GB');

		return $country;
	}
}
