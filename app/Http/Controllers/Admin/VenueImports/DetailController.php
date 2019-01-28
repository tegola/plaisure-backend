<?php

namespace App\Http\Controllers\Admin\VenueImports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VenueImport;

class DetailController extends Controller
{
	/**
	 * Get the data to show the venue import detail page.
	 * 
	 * @param  VenueImport  $venueImport
	 * @return Illuminate\Http\Response
	 */
	public function detail(VenueImport $venueImport)
	{
		// Load venue
		$venueImport->load('venue');

		return compact('venueImport');
	}
}