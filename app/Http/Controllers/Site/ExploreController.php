<?php

namespace App\Http\Controllers\Site;

use JavaScript;
use Illuminate\Http\Request;

use App;
use App\Venue;
use App\Category;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ExploreController extends Controller
{
	public function __construct(Request $request) {
		$this->what = $request->what;
		$this->near = $request->near;
		$this->page = $request->page ?: 1;
		$this->distance = config('constants.search_default_distance'); // FIXME: Move to bounds
		$this->center_lat = $request->has('center_lat') ? floatval($request->center_lat) : null;
		$this->center_lng = $request->has('center_lng') ? floatval($request->center_lng) : null;
		$this->ne_lat = $request->has('ne_lat') ? floatval($request->ne_lat) : null;
		$this->ne_lng = $request->has('ne_lng') ? floatval($request->ne_lng) : null;
		$this->sw_lat = $request->has('sw_lat') ? floatval($request->sw_lat) : null;
		$this->sw_lng = $request->has('sw_lng') ? floatval($request->sw_lng) : null;
	}

	public function index()
	{
		// Search
		$venues = $this->search();

		$search_params = [
			'what' => $this->what,
			'near' => $this->near,
			'page' => $this->page,
			'center_lat' => $this->center_lat,
			'center_lng' => $this->center_lng,
			'ne_lat' => $this->ne_lat,
			'ne_lng' => $this->ne_lng,
			'sw_lat' => $this->sw_lat,
			'sw_lng' => $this->sw_lng,
		];

		// Pass initial data to view
		Javascript::put([
			'searchParams' => $search_params,
			'venues' => $venues
		]);

		return view('site.venues.explore', [
			'what' => $this->what,
			'near' => $this->near,
			'venues' => $venues,
			'categories' => Category::all()
		]);
	}

	public function search()
	{
		// Find missing center and bounds by searching for the location name
		if ((!$this->ne_lat || !$this->ne_lng || !$this->sw_lat || !$this->sw_lng) && (!$this->center_lat || !$this->center_lng)) {
			if ($this->near && $position = $this->getPositionFromAddress($this->near)) {
				foreach($position as $key => $value) {
					$this->$key = $value;
				}
			} else {
				return response()->json([]);
			}
		}

		// Start loading venues
		$venues = Venue::with('categories');

		// Filter by bounds or by center
		if ($this->ne_lat && $this->ne_lng && $this->sw_lat && $this->sw_lng) {
			// Find in bounds
			$venues->inBounds($this->ne_lat, $this->ne_lng, $this->sw_lat, $this->sw_lng);
		} elseif ($this->center_lat && $this->center_lng) {
			// Find from center plus distance
			$venues->near($this->center_lat, $this->center_lng, $this->distance);
		}

		// Filter by name or category
		if ($this->what) {
			$venues->withNameOrCategory($this->what);
		}

		// Return results
		return $venues->simplePaginate(20, ['*'], 'page', $this->page); // paginate() does not work with the 'distance' column
	}

	/**
	 * Try to get center and bounds  of an address but stop gracefully if it
	 * doesn't work
	 *
	 * @param  String  $address
	 * @return Array
	 */
	private function getPositionFromAddress($address) {
		$api_url = "https://maps.googleapis.com/maps/api/geocode/json";
		// FIXME: Pass region per site and language per user locale
		$querystring = http_build_query(array(
			'key' => config('constants.google_maps_api_key'),
			'address' => $address,
			'language' => App::getLocale(),
			'region' => App::getLocale()
		));

		// Ask Google Maps and stop if it doesn't work
		$response = file_get_contents("{$api_url}?$querystring");
		if (!$response) return;

		// Check geocode results
		$geocode = json_decode($response);
		if ($geocode->status != 'OK') return;

		// Find lat and lng
		$geometry = $geocode->results[0]->geometry;

		return [
			'center_lat' => $geometry->location->lat,
			'center_lng' => $geometry->location->lng,
			'ne_lat' => $geometry->bounds->northeast->lat,
			'ne_lng' => $geometry->bounds->northeast->lng,
			'sw_lat' => $geometry->bounds->southwest->lat,
			'sw_lng' => $geometry->bounds->southwest->lng
		];
	}
}
