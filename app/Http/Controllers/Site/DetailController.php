<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;

use App\Models\Venue;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class DetailController extends Controller
{
	public function index(Venue $venue) {
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
			'venue' => $venue,
			'venue_category_string' => $venue_category_names->implode(','),
			'nearby_venues' => $nearby_venues
		]);
	}
}