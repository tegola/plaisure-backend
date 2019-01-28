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
	 * @return Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$venueImports = VenueImport::oldest('updated_at')
			->paginate(
				$request->input('perPage'),
				['*'],
				'page',
				$request->input('currentPage')
			);

		return compact('venueImports');
	}
}