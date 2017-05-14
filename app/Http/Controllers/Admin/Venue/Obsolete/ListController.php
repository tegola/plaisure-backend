<?php

namespace App\Http\Controllers\Admin\Venue\Obsolete;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\ImportedVenue;

class ListController extends Controller
{
	/**
	 * Shows the list of obsolete venues by scanning imported venues.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		// Get current venues' aams census codes
		$importedVenuesCensusCodes = ImportedVenue::pluck('aams_census_code')->all();

		// Find obsolete venues
		$venues = Venue::whereNotIn('aams_census_code', $importedVenuesCensusCodes);

		// Paginate
		$venues = $venues->paginate(50);

		return view('admin.venues.obsolete.list', compact('venues', 'showObsolete'));
	}
}