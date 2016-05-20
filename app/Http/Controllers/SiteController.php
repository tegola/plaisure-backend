<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Venue;
use App\Category;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SiteController extends Controller
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

	public function suggestions($what = null)
	{
		$venues = [];
		$categories = [];
		$suggestions = [];

		// Find venues and categories
		if ($what) {
			$venues = Venue::with('categories')->withNameOrCategory($what)->take(5)->get();
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
		$lat = $request->lat;
		$lng = $request->lng;
		$near = $request->near;
		$distance = 30; // km

		// Missing coordinates, find them by address
		if ($near && !$lat && !$lng && $position = $this->getLatLngFromAddress($near)) {
			$lat = $position['lat'];
			$lng = $position['lng'];
		}

		// Find venues complete with categories
		$venues = Venue::withNameOrCategory($what)
			->near($lat, $lng, $distance)
			->with('categories')
			->get();

		// Store position data in session
		session([
			'user.lat' => $lat,
			'user.lng' => $lng,
			'search.what' => $what,
			'search.near' => $near,
			'search.distance' => $distance
		]);

		return view('site.venues.list', [
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

		return view('site.venues.detail', [
			'lat' => session('user.lat') ?: null,
			'lng' => session('user.lng') ?: null,
			'what' => session('search.what') ?: null,
			'near' => session('search.near') ?: null,
			'distance' => session('search.distance') ?: null,
			'venue' => $venue,
			'nearby_venues' => $nearby_venues
		]);
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
		$querystring = http_build_query(array('address' => $address));

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