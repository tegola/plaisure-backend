<?php

namespace App\Http\Controllers\Admin\VenueImports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VenueImport;

class ListController extends Controller
{
	/**
	 * Get the data to show the venue import list.
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

		$venueImports = VenueImport::orderBy($sortBy, $sortDir)
			->when($filter, function($query, $filter) {
				return $query
					->orWhere('id', $filter)
					->orWhere('source_id', $filter)
					->orWhere('source_data', 'like', "%{$filter}%");
			})
			->paginate($perPage, ['*'], 'page', $currentPage);

		return compact('venueImports');
	}
}