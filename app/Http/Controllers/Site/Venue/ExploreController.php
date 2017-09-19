<?php

namespace App\Http\Controllers\Site\Venue;

use JavaScript;
use Illuminate\Http\Request;

use App;
use App\Models\Venue;
use App\Models\VenueCategory;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ExploreController extends Controller
{
	public function __construct(Request $request) {
		$this->near = $request->near;
		$this->categories = $request->filled('categories') ? $request->categories : VenueCategory::pluck('id')->all();
		// $this->distance = config('constants.search_default_distance'); // FIXME: Move to bounds
		$this->c_lat = $request->filled('c_lat') ? floatval($request->c_lat) : null;
		$this->c_lng = $request->filled('c_lng') ? floatval($request->c_lng) : null;
		$this->ne_lat = $request->filled('ne_lat') ? floatval($request->ne_lat) : null;
		$this->ne_lng = $request->filled('ne_lng') ? floatval($request->ne_lng) : null;
		$this->sw_lat = $request->filled('sw_lat') ? floatval($request->sw_lat) : null;
		$this->sw_lng = $request->filled('sw_lng') ? floatval($request->sw_lng) : null;
	}

	public function index()
	{
		// Make sure we have all location data
		// if (!$this->hasLocationData()) {
		// 	return back();
		// }

		$searchParams = [
			'near' => $this->near,
			'categories' => $this->categories,
			'c_lat' => $this->c_lat,
			'c_lng' => $this->c_lng,
			'ne_lat' => $this->ne_lat,
			'ne_lng' => $this->ne_lng,
			'sw_lat' => $this->sw_lat,
			'sw_lng' => $this->sw_lng,
		];

		$categories = VenueCategory::all();

		// Pass initial data to view
		Javascript::put(compact('searchParams'));

		return view('site.venues.explore', [
			'near' => $this->near,
			'categories' => $categories
		]);
	}

	public function search()
	{
		// Make sure we have all location data
		if (!$this->hasLocationData()) {
			return response()->json([]);
		}

		// Start loading venues
		$venues = Venue::with(['categories']);

		// Filter by bounds
		if ($this->ne_lat && $this->ne_lng && $this->sw_lat && $this->sw_lng) {
			// Find in bounds
			$venues->inBounds($this->ne_lat, $this->ne_lng, $this->sw_lat, $this->sw_lng);
		}

		// Calculate distance
		if ($this->c_lat && $this->c_lng) {
			$venues->withDistanceFrom($this->c_lat, $this->c_lng);
		}

		// Filter by category
		if ($this->categories) {
			$venues->whereHas('categories', function($query) {
				$query->whereIn('id', $this->categories);
			});
		}

		// Return results
		return $venues->simplePaginate(100);
	}

	private function hasLocationData()
	{
		if (!$this->ne_lat || !$this->ne_lng || !$this->sw_lat || !$this->sw_lng || !$this->c_lat || !$this->c_lng) {
			return $this->getLocationData();
		}

		return true;
	}

	/**
	 * Get center and bounds with Google Maps geocoder
	 *
	 * @return boolean
	 */
	private function getLocationData()
	{
		// Stop if no location name is provided
		if (!$this->near) {
			return false;
		}

		$api_url = "https://maps.googleapis.com/maps/api/geocode/json";
		$querystring = http_build_query(array(
			'key' => config('constants.google_maps_api_key'),
			'address' => $this->near,
			'language' => App::getLocale(),
			'region' => App::getLocale()
		));

		// Ask Google Maps and stop if it doesn't work
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
}
