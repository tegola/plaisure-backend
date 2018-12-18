<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\VenueCategory;
use App\Transformers\VenueTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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

	/**
	 * Load the data for the home page
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function data()
	{
		$categories = VenueCategory::forCountry(locale_get_region(app()->getLocale()))
			->select('id', 'machine_name', 'name')
			->get();
		$cacheLimit = 1;

		// Highlights - 2 taken from the latest 20 (1/10 chance to appear)
		$highlightedVenues = Cache::remember('home.highlights', $cacheLimit, function() {
			$venues = $this->initQuery()
				->whereHas('subscriptions', function($query) {
					$query->where('name', 'premium_2'); // FIXME: where subscription has a field "home_page_highlight"
				})
				->latest()
				->take(20);

			// Get at least 2
			if ($venues->count() >= 2) {
				$venues = $venues->get()->random(2);
				$venues = $this->transformVenues($venues);
			} else {
				$venues = [];
			}

			return $venues;
		});


		// New - 8 taken from the latest 36 (1/4 chance to appear)
		$newVenues = Cache::remember('home.new', $cacheLimit, function() {
			$venues = $this->initQuery()
				->latest()
				->take(36)
				->get()
				->random(9);

			return $this->transformVenues($venues);
		});

		return compact(
			'categories',
			'highlightedVenues',
			'newVenues'
		);
	}

	/**
	 * Init venue query with satellite data.
	 * 
	 * @param  \Illuminate\Database\Builder
	 */
	private function initQuery() {
		return Venue::query()
			->with('categories', 'businessHours')
			->with(['photos' => function($query) {
				$query->take(1);
			}]);
	}

	/**
	 * Transform venues using VenueTransformer.
	 * 
	 * @param  \Illuminate\Support\Collection $venues
	 * @return array
	 */
	private function transformVenues($venues) {
		return $venues
			->transformWith(new VenueTransformer())
			->parseIncludes([
				'categories',
				'photos',
				'business_hours'
			]);
	}
}