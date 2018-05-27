<?php

namespace App\Http\Controllers\Site\Venues;

use JavaScript;
use Illuminate\Http\Request;

use App;
use App\Models\Venue;
use App\Models\VenueCategory;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Transformers\VenueTransformer;
use App\Transformers\VenueCategoryTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class ExploreController extends Controller
{
	public function __construct(Request $request) {
		$this->query = $request->input('query');

		$categories = $request->filled('categories') ? $request->input('categories') : VenueCategory::pluck('id')->all();
		$this->categories = array_map(function($id) { // pluck() returns IDs as strings
			return (int) $id;
		}, $categories);

		// $this->amenities = $request->filled('amenities') ? $request->input('amenities') : [];
		$this->radius = $request->filled('radius') ? intval($request->input('radius')) : config('constants.search_radiuses')[0];
		$this->c_lat = $request->filled('c_lat') ? floatval($request->input('c_lat')) : null;
		$this->c_lng = $request->filled('c_lng') ? floatval($request->input('c_lng')) : null;
		$this->ne_lat = $request->filled('ne_lat') ? floatval($request->input('ne_lat')) : null;
		$this->ne_lng = $request->filled('ne_lng') ? floatval($request->input('ne_lng')) : null;
		$this->sw_lat = $request->filled('sw_lat') ? floatval($request->input('sw_lat')) : null;
		$this->sw_lng = $request->filled('sw_lng') ? floatval($request->input('sw_lng')) : null;
	}

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

	public function index()
	{
		// Prepare initial data
		$searchParams = [
			'query' => $this->query,
			'categories' => $this->categories,
			// 'amenities' => $this->amenities,
			'radius' => $this->radius,
			'c_lat' => $this->c_lat,
			'c_lng' => $this->c_lng,
			'ne_lat' => $this->ne_lat,
			'ne_lng' => $this->ne_lng,
			'sw_lat' => $this->sw_lat,
			'sw_lng' => $this->sw_lng,
		];

		$radiuses = config('constants.search_radiuses');
		$categories = VenueCategory::all();
		// $amenities = $this->amenities()->all();

		// Pass initial data to view
		Javascript::put(compact('searchParams', 'radiuses', 'categories'/*, 'amenities'*/));

		return view('site.venues.explore', [
			'query' => $this->query,
			'categories' => $categories
		]);
	}

	public function search()
	{
		// Make sure we have all location data
		if (!$this->hasCenter() && !$this->hasBounds()) return response()->json([]);

		// Start loading venues
		$venues = Venue::with(['categories']);

		// Find by bounds or center
		if ($this->hasBounds()) {
			$venues->inBounds($this->ne_lat, $this->ne_lng, $this->sw_lat, $this->sw_lng);
		} elseif ($this->hasCenter()) {
			$venues->withDistanceFrom($this->c_lat, $this->c_lng);

			// Limit by radius
			if ($this->radius) {
				$venues
					->having('distance', '<=', $this->radius)
					->orHaving('distance_with_bonus', '<=', $this->radius);
			}
		}

		// Filter by category
		if ($this->categories) {
			$venues->whereHas('categories', function($query) {
				$query->whereIn('id', $this->categories);
			});
		}

		// Filter by amenities
		/*
		if ($this->amenities) {
			$amenities = $this->amenities();

			foreach ($this->amenities as $amenity) {
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

	private function hasCenter()
	{
		return $this->c_lat && $this->c_lng;
	}

	private function hasBounds()
	{
		return $this->ne_lat && $this->ne_lng && $this->sw_lat && $this->sw_lng;
	}

	/**
	 * Get center and bounds with Google Maps geocoder
	 *
	 * @return boolean
	 */
	/*
	private function geocode()
	{
		// Stop if no location name is provided
		if (!$this->query) return false;

		// Ask Google Maps
		$api_url = "https://maps.googleapis.com/maps/api/geocode/json";
		$querystring = http_build_query(array(
			'key' => // GOOGLE MAPS API KEY HERE
			'address' => $this->query,
			'language' => App::getLocale(),
			'region' => App::getLocale()
		));

		// Stop if it didn't work
		$response = file_get_contents("{$api_url}?$querystring");
		if (!$response) return false;

		// Check geocode results
		$geocode = json_decode($response);
		if ($geocode->status != 'OK') return false;

		// Find coords
		$geometry = $geocode->results[0]->geometry;

		$this->c_lat = $geometry->location->lat;
		$this->c_lng = $geometry->location->lng;
		$this->ne_lat = $geometry->bounds->northeast->lat;
		$this->ne_lng = $geometry->bounds->northeast->lng;
		$this->sw_lat = $geometry->bounds->southwest->lat;
		$this->sw_lng = $geometry->bounds->southwest->lng;

		return true;
	}
	*/
}
