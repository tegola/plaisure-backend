<?php

namespace App\Http\Controllers\Admin\Venues;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\ImportedVenue;

class ObsoleteListController extends Controller
{
	/**
	 * Shows the list of obsolete venues by scanning imported venues.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		// Go to upload if no imported venues are found
		if (!ImportedVenue::count()) return redirect()->route('admin.venues.import.edit');

		// Get current venues' aams census codes
		$importedVenuesCensusCodes = ImportedVenue::pluck('aams_census_code')->all();

		// Find obsolete venues
		$venues = Venue::whereNotIn('aams_census_code', $importedVenuesCensusCodes);

		// Paginate
		$venues = $venues->paginate(50);

		return view('admin.venues.obsolete.list', compact('venues', 'showObsolete'));
	}
}