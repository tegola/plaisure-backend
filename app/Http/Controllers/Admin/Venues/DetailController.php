<?php

namespace App\Http\Controllers\Admin\Venues;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Venue;

class DetailController extends Controller
{
	/**
	 * Get the data to show the venue detail page.
	 * 
	 * @param  Venue  $venue
	 * @return Illuminate\Http\Response
	 */
	public function detail(Venue $venue)
	{
		return compact('venue');
	}
}