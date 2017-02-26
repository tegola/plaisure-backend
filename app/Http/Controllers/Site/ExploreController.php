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
		$this->page = $request->page ?: 1;
		$this->what = $request->what;
		$this->lat = floatval($request->lat);
		$this->lng = floatval($request->lng);
		$this->near = $request->near;
		$this->distance = config('constants.search_default_distance'); // FIXME: Move to bounds
		//$this->bounds = $request->bounds;
	}

	public function render()
	{
		// Search
		$venues = $this->search();

		// Pass venues to javascript
		Javascript::put([
			'lat' => $this->lat,
			'lng' => $this->lng,
			'what' => $this->what,
			'near' => $this->near,
			'venues' => $venues->toArray()
		]);

		return view('site.venues.explore', [
			//'lat' => $this->lat,
			//'lng' => $this->lng,
			'what' => $this->what,
			'near' => $this->near,
			'venues' => $venues,
			'categories' => Category::all()
		]);
	}

	public function search()
	{
		// No specified location or missing coordinates, return empty array
		if (!$this->near && !$this->lat && !$this->lng) {
			return [];
		}

		// Missing coordinates, find them by address, or return empty array
		if ($this->near && !$this->lat && !$this->lng && $position = $this->getLatLngFromAddress($this->near)) {
			$this->lat = $position['lat'];
			$this->lng = $position['lng'];
		}

		// Find venues complete with categories
		return Venue::withNameOrCategory($this->what)
			->near($this->lat, $this->lng, $this->distance)
			->with('categories')
			->simplePaginate(20, ['*'], 'page', $this->page); // paginate() does not work with the 'distance' column
	}

	/**
	 * Try to get latitude and longitude of an address
	 * but stop gracefully if it doesn't work
	 *
	 * @param  String  $address
	 * @return Array
	 */
	private function getLatLngFromAddress($address) {
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
		$location = $geocode->results[0]->geometry->location;

		return [
			'lat' => $location->lat,
			'lng' => $location->lng
		];
	}
}
