<?php

namespace App\Http\Controllers;

use JavaScript;
use Illuminate\Http\Request;

use App\Venue;
use App\Category;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class VenueController extends Controller
{
	public function index()
	{
		return view('site.home', [
			'lat' => session('user.lat', null),
			'lng' => session('user.lng', null),
			'what' => session('search.what', null),
			'near' => session('search.near', null),
			'distance' => session('search.distance', null)
		]);
	}

	public function suggestions(Request $request)
	{
		$what = trim($request->what);
		$lat = $request->lat;
		$lng = $request->lng;
		$near = $request->near;
		$venues = [];
		$categories = [];
		$suggestions = [];

		// Find venues and categories
		if ($what) {
			$venues = Venue::with('categories')->withNameOrCategory($what);
			if ($lat && $lng) {
				$venues = $venues->near($lat, $lng, 20);
			}
			$venues = $venues->take(5)->get();
			$categories = Category::where('name', 'like', "%{$what}%")->take(5)->get();
		} else {
			$categories = Category::take(5)->get();
		}

		// Prepare suggestions (categories first)
		foreach ($categories as $c) {
			array_push($suggestions, [
				"type" => "category",
				"id" => $c->id,
				"name" => $c->name
			]);
		}
		foreach ($venues as $v) {
			array_push($suggestions, [
				"type" => "venue",
				"id" => $v->id,
				"name" => $v->name,
				"category" => $v->categories()->first()->name,
				"city" => $v->address_city
			]);
		}

		return response()->json($suggestions);
	}

	public function explore(Request $request)
	{
		$what = $request->what;
		$lat = $request->lat ? floatval($request->lat) : session('user.lat');
		$lng = $request->lng ? floatval($request->lng) : session('user.lng');
		$near = $request->near ?: session('search.near');
		$distance = $request->distance ?: session('search.distance') ?: config('constants.search_default_distance');

		// No specified location or missing coordinates, go back to home page
		if (!$near && !$lat && !$lng) {
			return redirect()->route('site.home');
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

		// Store position data in session
		session([
			'user.lat' => $lat,
			'user.lng' => $lng,
			'search.what' => $what,
			'search.near' => $near,
			'search.distance' => $distance
		]);

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
			'distance' => $distance,
			'venues' => $venues
		]);
	}

	public function detail(Venue $venue) {
		// Get nearby venues
		$nearby_venues = Venue::near($venue->geo_latitude, $venue->geo_longitude, 5)
			->where('id', '!=', $venue->id)
			->take(5)
			->get();

		// Prepare categories string
		$venue_category_names = $venue->categories->map(function($cat){
			return $cat->name;
		});

		return view('site.venues.detail', [
			'lat' => session('user.lat') ?: null,
			'lng' => session('user.lng') ?: null,
			'what' => session('search.what') ?: null,
			'near' => session('search.near') ?: null,
			'distance' => session('search.distance') ?: null,
			'venue' => $venue,
			'venue_category_string' => $venue_category_names->implode(','),
			'nearby_venues' => $nearby_venues
		]);
	}

	public function claim() {
		return view('site.venues.claim');
	}

	/**
	 * Try to get latitude and longitude of an address
	 * but stop gracefully if it doesn't work
	 *
	 * @param  String  $address
	 * @return Array
	 */
	private function getLatLngFromAddress($address) {
		$api_url = "http://maps.googleapis.com/maps/api/geocode/json";
		// FIXME: Pass region per site and language per user locale
		$querystring = http_build_query(array(
			'address' => $address,
			'language' => 'it',
			'region' => 'IT'
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