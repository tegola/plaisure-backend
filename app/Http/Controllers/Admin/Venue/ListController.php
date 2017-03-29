<?php

namespace App\Http\Controllers\Admin\Venue;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;

class ListController extends Controller
{
	/**
	 * Shows the venue list.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		// Load venues sorted by update date
		$venues = Venue::oldest('updated_at');

		// Search
		if ($request->has('query')) {
			$query = $request->input('query');

			$venues->where('name', 'like', "%{$query}%")
					->orWhere('address_city', 'like', "%{$query}%")
					->orWhere('address_province', 'like', "%{$query}%")
					->orWhere('aams_census_code', 'like', "%{$query}%");
		}

		// Paginate
		$venues = $venues->paginate(50);

		$venues->appends($request->all());

		// Pass old values
		$request->flash();

		return view('admin.venues.list', compact('venues'));
	}
}