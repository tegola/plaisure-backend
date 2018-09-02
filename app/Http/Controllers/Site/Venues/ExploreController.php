<?php

namespace App\Http\Controllers\Site\Venues;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\VenueCategory;
use App\Transformers\VenueTransformer;
use App\Transformers\VenueCategoryTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
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
	 * @return Illuminate\Http\Response
	 */
	public function data()
	{
		$categories = VenueCategory::all();
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
		// $query = $request->input('query');

		$categories = $request->filled('categories') ? $request->input('categories') : VenueCategory::pluck('id')->all();
		$categoryIds = array_map(function($id) { // pluck() returns IDs as strings
			return (int) $id;
		}, $categories);
		// $amenityIds = $request->filled('amenities') ? $request->input('amenities') : [];
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
		$venues = Venue::with(['categories']);

		// Find by bounds or center
		if ($ne_lat && $ne_lng && $sw_lat && $sw_lng) {
			$venues->inBounds($ne_lat, $ne_lng, $sw_lat, $sw_lng);
		} elseif ($c_lat && $c_lng) {
			$venues->withDistanceFrom($c_lat, $c_lng);

			// Limit by radius
			if ($radius) {
				$venues
					->having('distance', '<=', $radius)
					->orHaving('distance_with_bonus', '<=', $radius);
			}
		}

		// Filter by category
		if ($categoryIds) {
			$venues->whereHas('categories', function($query) use ($categoryIds) {
				$query->whereIn('id', $categoryIds);
			});
		}

		// Filter by amenities
		/*
		if ($amenityIds) {
			$amenities = $this->amenities();

			foreach ($amenities as $amenity) {
				// Get amenity object
				$currentAmenity = $amenities->where('machine_name', $amenity)->first();

				// Skip if is not a valid amenity
				if (!$currentAmenity) continue;

				$venues->orWhere($currentAmenity['field'], true);
			}
		}
		*/

		// Load first photo
		$venues->with(['photos' => function($query) {
			$query->take(1);
		}]);

		// Return results
		// We take a maximum of 100 venues, and the client knows it's the max
		// number it can get. We did it to avoid using simplePaginate(), which
		// is not supported by Fractal transformers, and to avoid paginate(),
		// which don't work with MySQL HAVINGs
		$venues = $venues
			->take(100)
			->get()
			->transformWith(new VenueTransformer())
			->includeCategories()
			->includePhotos();

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
}
