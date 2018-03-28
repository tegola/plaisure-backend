<?php

namespace App\Http\Controllers\Site\Venues;

use Illuminate\Http\Request;

use App\Models\Venue;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JavaScript;

class DetailController extends Controller
{
	/**
	 * Redirect /venues/{id} to /venues/{id_hashed} or shows the 404 page.
	 * FIXME: Remove whene there are no more hits.
	 * 
	 * @param  int $id The venue id
	 * @return Illuminate\Http\Response
	 */
	public function redirect($id) {
		$venue = Venue::find($id);

		// Stop if venue doesn't exist
		abort_if(!$venue, 404);

		// Redirect to venue with hashed id
		return redirect(route('site.venues.detail', $venue), 301);
	}

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
			$nearbyVenues = Venue::near($venue->geo_latitude, $venue->geo_longitude, 5)
				->where('id', '!=', $venue->id)
				->take(3)
				->get();
		} else {
			$nearbyVenues = null;
		}

		// Prepare categories string
		$venueCategoryString = $venue->categories->slice(0, 2)->pluck('name')->implode(', ');

		// Send data to javascript
		JavaScript::put(compact('venue'));

		return view('site.venues.detail', compact(
			'venue',
			'venueCategoryString',
			'nearbyVenues'
		));
	}
}