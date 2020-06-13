<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
	/**
	 * Get distinct venue contries.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function countries()
	{
		return DB::table('venues')
			->distinct()
			->select('country')
			->where('country', '!=', '')
			->orderBy('country')
			->pluck('country');
	}

	/**
	 * Get venue ids for the specified country.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function venues(Request $request)
	{
		return DB::table('venues')
			->select('id_hashed')
			->where('country', '!=', '')
			->where('country', $request->country)
			->orderBy('id_hashed')
			->pluck('id_hashed');
	}
}
