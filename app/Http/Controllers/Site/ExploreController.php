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
	public function render(Request $request)
	{
		$what = $request->what;
		$lat = floatval($request->lat);
		$lng = floatval($request->lng);
		$near = $request->near;
		//$bounds = $request->bounds;
		$distance = config('constants.search_default_distance'); // FIXME: Move to bounds

		// No specified location or missing coordinates
		if (!$near && !$lat && !$lng) {
			return redirect()->route('site.home'); // FIXME: Find user location and prefill the textbox
		}

		// Missing coordinates, find them by address
		if ($near && !$lat && !$lng && $position = $this->getLatLngFromAddress($near)) {
			$lat = $position['lat'];
			$lng = $position['lng'];
		}

		// Find venues complete with categories
		$venues = Venue::withNameOrCategory($what)
			->near($lat, $lng, $distance)
			->with('categories')
			->simplePaginate();
			// ->take(20)
			// ->get();

		// Pass venues to javascript
		Javascript::put([
			'lat' => $lat,
			'lng' => $lng,
			'what' => $what,
			'near' => $near,
			'distance' => $distance,
			'venues' => $venues->toArray()
		]);

		return view('site.venues.explore', [
			'lat' => $lat,
			'lng' => $lng,
			'what' => $what,
			'near' => $near,
			'venues' => $venues
		]);
	}

	public function search(Request $request)
	{
		$page = $request->page ?: 1;
		$what = $request->what;
		$lat = floatval($request->lat);
		$lng = floatval($request->lng);
		$near = $request->near;
		$distance = config('constants.search_default_distance'); // FIXME: Move to bounds

		// No specified location or missing coordinates, return empty array
		if (!$near && !$lat && !$lng) {
			return [];
		}

		// Missing coordinates, find them by address
		if ($near && !$lat && !$lng && $position = $this->getLatLngFromAddress($near)) {
			$lat = $position['lat'];
			$lng = $position['lng'];
		}

		// Find venues complete with categories
		return Venue::withNameOrCategory($what)
			->near($lat, $lng, $distance)
			->with('categories')
			->simplePaginate(20, ['*'], 'page', $page); // paginate() does not work with the 'distance' column
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
