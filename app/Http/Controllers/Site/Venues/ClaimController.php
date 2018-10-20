<?php

namespace App\Http\Controllers\Site\Venues;

use Illuminate\Http\Request;
use App\Models\Venue;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Transformers\VenueTransformer;

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
	 * @return Illuminate\Http\Response
	 */
	public function load(Venue $venue) {
		$this->authorize('claim', $venue);

		// Load first photo
		$venue->with(['photos' => function($query) {
			$query->take(1);
		}]);

		$codeRequired = $venue->aams_census_code ? true : false;

		// Prepare venue
		$venue = fractal($venue, new VenueTransformer())
			->includePhotos()
			->includeCategories();

		return compact('venue', 'codeRequired');
	}

	public function confirm(Venue $venue, Request $request) {
		$this->authorize('claim', $venue);

		// Validate aams census code if needed
		if ($venue->aams_census_code) {
			$request->validate([
				'code' => "required|in:{$venue->aams_census_code}"
			]);
		}

		// Assign venue to user
		$venue->owner_id = auth()->user()->id;
		$venue->save();

		return null;
	}
}