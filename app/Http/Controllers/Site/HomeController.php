<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\VenueCategory;
use App\Transformers\VenueTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
	/**
	 * Load the data for the home page
	 *
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function data(Request $request)
	{
		$country = $this->extractCountry($request);

		$categories = VenueCategory::forCountry($country)
			->select('id', 'machine_name')
			->get();
		$cacheLimit = now()->addHour();

		// Highlights - 2 taken from the latest 20 (1/10 chance to appear)
		$highlightedVenues = Cache::remember("{$country}.home.highlights.", $cacheLimit, function() use($country) {
			$venues = $this->initQuery($country)
				->has('photos')
				->whereHas('subscriptions', function($query) {
					$query->where('name', 'premium_2'); // FIXME: where subscription has a field "home_page_highlight"
				})
				->latest()
				->take(20);

			// Get at least 2
			if ($venues->count() >= 2) {
				$venues = $venues
					->get()
					->each(function($venue) {
						$venue->load([
							'photos' => function($query) {
								return $query->take(1);
							}
						]);
					})
					->random(2);
				$venues = $this->transformVenues($venues);
			} else {
				$venues = [];
			}

			return $venues;
		});

		// New - 9 taken from the latest 36 (1/4 chance to appear)
		$newVenues = Cache::remember("{$country}.home.new", $cacheLimit, function() use($country) {
			$venues = $this->initQuery($country)
				->latest()
				->take(36);

			// Get exactly 9
			if ($venues->count() >= 9) {
				$venues = $venues
					->get()
					->each(function($venue) {
						$venue->load([
							'photos' => function($query) {
								return $query->take(1);
							}
						]);
					})
					->random(9);
				$venues = $this->transformVenues($venues);
			} else {
				$venues = [];
			}

			return $venues;
		});

		return compact('categories', 'highlightedVenues', 'newVenues');
	}

	/**
	 * Init venue query with satellite data.
	 * 
	 * @param  \Illuminate\Database\Builder
	 */
	private function initQuery($country) {
		return Venue::query()
			->where('country', $country)
			->with('categories', 'businessHours');
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

	/**
	 * Find the country for the user, or use a default.
	 *
	 * @param  Request $request
	 * @return string
	 */
	private function extractCountry(Request $request)
	{
		$user = auth()->user();
		$country = $user ? locale_get_region($user->locale) : $request->input('country', 'GB');

		return $country;
	}
}