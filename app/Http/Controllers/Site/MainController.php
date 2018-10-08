<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\VenueCategory;
use App\Transformers\VenueTransformer;

class MainController extends Controller
{
	/**
	 * Show the home page.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('site.layout');
	}

	public function data()
	{
		$categories = VenueCategory::select('id', 'machine_name')->get();

		// Load latest venues
		$venues = Venue::query()
			->with('categories', 'businessHours')
			->with(['photos' => function($query) {
				$query->take(1);
			}])
			->latest()
			->take(10)
			->get()
			->transformWith(new VenueTransformer())
			->parseIncludes([
				'categories',
				'photos',
				'business_hours'
			]);

		return compact('categories', 'venues');
	}
}