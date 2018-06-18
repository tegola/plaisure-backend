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
			['field' => 'amenity_atm',             'machine_name' => 'atm',             'name' => 'Totem Bancomat'],
			['field' => 'amenity_bar',             'machine_name' => 'bar',             'name' => 'Bar'],
			['field' => 'amenity_pay_per_view',    'machine_name' => 'pay_per_view',    'name' => 'Pay per view'],
			['field' => 'amenity_pos',             'machine_name' => 'pos',             'name' => 'POS'],
			['field' => 'amenity_private_parking', 'machine_name' => 'private_parking', 'name' => 'Parcheggio privato'],
			['field' => 'amenity_restaurant',      'machine_name' => 'restaurant',      'name' => 'Ristorante'],
			['field' => 'amenity_security',        'machine_name' => 'security',        'name' => 'Servizio di sicurezza'],
			['field' => 'amenity_smoking_area',    'machine_name' => 'smoking_area',    'name' => 'Area fumatori'],
			['field' => 'amenity_wifi',            'machine_name' => 'wifi',            'name' => 'Wi-Fi']
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
		
		return compact('categories');

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
			// var_export($categoryIds);
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
}
