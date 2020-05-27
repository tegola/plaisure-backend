<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Resources\VenueCategory as VenueCategoryResource;
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
		$country = $request->country;
		$categories = VenueCategory::forCountry($country)->get();
		$cacheLimit = 3600;

		// Highlights - 2 taken from the latest 20 (1/10 chance to appear)
		$highlightedVenues = Cache::remember("{$country}.home.highlights", $cacheLimit, function() use($country) {
			$venues = $this->initQuery($country)
				->whereHas('subscriptions', function($query) {
					$query
						->active()
						->where('home_page_highlight', true);
				})
				->has('photos')
				->latest()
				->take(20);

			// Get at least 2
			if ($venues->count() >= 2) {
				$venues = $venues
					->get()
					->random(2);
				$venues = $this->transformVenues($venues);
			} else {
				$venues = [];
			}

			return $venues;
		});

		// New - 9 taken from the latest 90 (1/10 chance to appear)
		$newVenues = Cache::remember("{$country}.home.new", $cacheLimit, function() use($country) {
			$venues = $this->initQuery($country)
				->latest()
				->take(90);

			// Get exactly 9
			if ($venues->count() >= 9) {
				$venues = $venues
					->get()
					->random(9);
				$venues = $this->transformVenues($venues);
			} else {
				$venues = [];
			}

			return $venues;
		});

		return [
			'categories' => VenueCategoryResource::collection($categories),
			'highlightedVenues' => $highlightedVenues,
			'newVenues' => $newVenues
		];
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
			->each(function($venue) { // Load first photo (limit/take doesn't work with eager loading)
				$venue->load(['photos' => function($query) {
					$query->take(1);
				}]);
			})
			->transformWith(new VenueTransformer())
			->parseIncludes([
				'categories',
				'photos',
				'business_hours'
			]);
	}
}
