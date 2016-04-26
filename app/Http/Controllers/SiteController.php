<?php

namespace App\Http\Controllers;

use Request;

use App\Venue;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SiteController extends Controller
{

	public function index()
	{
		return view('site.home', [
			'lat' => session('user.lat', null),
			'lng' => session('user.lng', null),
			'query' => session('search.query', null),
			'near' => session('search.near', null),
			'distance' => session('search.distance', null)
		]);
	}

	public function explore()
	{
		$query = Request::input('query');
		$lat = Request::input('lat');
		$lng = Request::input('lng');
		$near = Request::input('near');
		$distance = 30; // km

		// Missing coordinates, find them by address
		if ($near && !$lat && $lng) {
			$position = $this->getLatLngFromAddress($near);

			if ($position) {
				$lat = $position['lat'];
				$lng = $position['lng'];
			}
		}

		// Find venues
		$venues = Venue::near($lat, $lng, $distance)
			->where('name', 'like', "%{$query}%")
			->get();

		// Store position data in session
		session([
			'user.lat' => $lat,
			'user.lng' => $lng,
			'search.query' => $query,
			'search.near' => $near,
			'search.distance' => $distance
		]);

		return view('site.venues.list', [
			'lat' => $lat,
			'lng' => $lng,
			'query' => $query,
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
			'query' => session('search.query') ?: null,
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