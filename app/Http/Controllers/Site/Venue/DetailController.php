<?php

namespace App\Http\Controllers\Site\Venue;

use Illuminate\Http\Request;

use App\Models\Venue;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon;

class DetailController extends Controller
{
	public function index(Venue $venue) {
		// Get nearby venues (if the plan allows it)
		if (!$venue->plan || !$venue->plan->hide_nearby_venues) {
			$nearby_venues = Venue::near($venue->geo_latitude, $venue->geo_longitude, 5)
				->where('id', '!=', $venue->id)
				->take(3)
				->get();
		} else {
			$nearby_venues = null;
		}

		// Prepare categories string
		$venue_category_string = $venue->categories->pluck('name')->implode(', ');

		// Get today's date
		$today = Carbon::now();

		return view('site.venues.detail', compact(
			'venue',
			'venue_category_string',
			'nearby_venues',
			'today'
		));
	}
}