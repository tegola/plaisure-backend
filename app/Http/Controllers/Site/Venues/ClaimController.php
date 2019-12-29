<?php

namespace App\Http\Controllers\Site\Venues;

use App\Http\Controllers\Controller;
use App\Http\Resources\Venue as VenueResource;
use App\Models\Venue;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:api');
	}

	/**
	 * Get the data to show the venue claim page.
	 * 
	 * @param  Venue  $venue
	 * @return \Illuminate\Http\Response
	 */
	public function load(Venue $venue) {
		$this->authorize('claim', $venue);

		// Load relationships
		$venue->load([
			'categories',
			'photos' => function($query) {
				$query->take(1);
			}
		]);

		return [
			'venue' => new VenueResource($venue)
		];
	}

	public function confirm(Venue $venue, Request $request) {
		$this->authorize('claim', $venue);

		// Assign venue to user
		$venue->owner_id = auth()->user()->id;
		$venue->save();

		return null;
	}
}