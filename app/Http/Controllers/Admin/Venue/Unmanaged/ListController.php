<?php

namespace App\Http\Controllers\Admin\Venue\Unmanaged;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\ImportedVenue;

class ListController extends Controller
{
	/**
	 * Find unmanaged venues by scanning imported ones.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		// Go to upload if no imported venues are found
		if (!ImportedVenue::count()) return redirect()->route('admin.venues.import.edit');

		// Get current venues' aams census codes
		$venuesCensusCodes = Venue::pluck('aams_census_code')->all();

		// Find new venues
		$importedVenues = ImportedVenue::whereNotIn('aams_census_code', $venuesCensusCodes)
			->orderBy('name');

		// Search
		if ($request->has('query')) {
			$importedVenues = $importedVenues->search($request->input('query'));
		}

		// Paginate
		$importedVenues = $importedVenues->paginate(50);
		$importedVenues->appends($request->all());

		// Pass old values
		$request->flash();

		return view('admin.venues.unmanaged.list', compact('importedVenues'));
	}
}