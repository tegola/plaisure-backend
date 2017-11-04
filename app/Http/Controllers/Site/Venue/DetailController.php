<?php

namespace App\Http\Controllers\Site\Venue;

use Illuminate\Http\Request;

use App\Models\Venue;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon;
use JavaScript;

class DetailController extends Controller
{
	/**
	 * Show the venue detail page.
	 * 
	 * @param  Venue  $venue [description]
	 * @return Illuminate\Http\Response
	 */
	public function index(Venue $venue) {
		// Load venue photos
		$venue->load('photos');

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
		$venue_category_string = $venue->categories->slice(0, 2)->pluck('name')->implode(', ');

		// Get today's date
		$today = Carbon::now();

		// Send data to javascript
		JavaScript::put(compact('venue'));

		return view('site.venues.detail', compact(
			'venue',
			'venue_category_string',
			'nearby_venues',
			'today'
		));
	}
}