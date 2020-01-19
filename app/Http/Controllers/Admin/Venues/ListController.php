<?php

namespace App\Http\Controllers\Admin\Venues;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Venue;

class ListController extends Controller
{
	/**
	 * Get the data to show the venue list.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$currentPage = $request->query('currentPage', 1);
		$perPage = $request->query('perPage', 20);
		$sortBy = $request->query('sortBy', 'updated_at');
		$sortDir = $request->query('sortDesc', 'true') == 'true' ? 'desc' : 'asc';
		$filter = $request->query('filter');
		$view = $request->query('view');

		$query = Venue::orderBy($sortBy, $sortDir)
			->when($filter, function($query, $filter) {
				return $query
					->orWhere('id_hashed', $filter)
					->orWhere('name', 'like', "%{$filter}%")
					->orWhere('address_line1', 'like', "%{$filter}%")
					->orWhere('address_line2', 'like', "%{$filter}%")
					->orWhere('address_city', 'like', "%{$filter}%")
					->orWhere('address_postcode', 'like', "%{$filter}%")
					->orWhere('address_province', 'like', "%{$filter}%")
					->orWhere('address_region', 'like', "%{$filter}%");
			});

		// Filter for "linked only"
		switch ($view) {
			case 'linked':
				$query->has('import');
				break;

			case 'outdated':
				$query->whereHas('import', function($query) {
					return $query->whereColumn('venue_imports.updated_at', '>', 'venues.updated_at');
				});
				break;

			case 'unlinked':
				$query->doesntHave('import');
				break;
		}

		$venues = $query->paginate($perPage, ['*'], 'page', $currentPage);

		return compact('venues');
	}

	/**
	 * Shows the list of obsolete venues by scanning imported venues.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	/*
	public function obsolete()
	{
		// Get current venues' aams census codes
		$importedVenuesCensusCodes = ImportedVenue::pluck('_____')->all();

		// Find obsolete venues
		$venues = Venue::whereNotIn('_____', $importedVenuesCensusCodes);

		// Paginate
		$venues = $venues->paginate(50);

		return view('admin.venues.obsolete-list', compact('venues', 'showObsolete'));
	}
	*/
}