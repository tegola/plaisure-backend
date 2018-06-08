<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;

use App\Models\Venue;
use App\Models\VenueCategory;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
	/**
	 * Prepare category and venue suggestions, the latter only if location data
	 * is present.
	 * 
	 * @param  Request $request
	 * @return array
	 */
	public function suggestions(Request $request)
	{
		$query = trim($request->input('query'));
		$venues = [];
		$categories = [];
		$suggestions = [];

		// Find venues and categories
		if ($query) {
			$tokens = explode(' ', $query);

			// Venues
			$venuesQuery = Venue::with('categories');

			// Find in venue name
			foreach ($tokens as $token) {
				$venuesQuery->where('name', 'like', "%{$token}%");
			}

			// Find in categories name
			$venuesQuery->orWhereHas('categories', function($query) use ($tokens) {
				foreach ($tokens as $token) {
					$query->where('name', 'like', "%{$token}%");
				}
			});

			$venues = $venuesQuery
				->latest()
				->take(5)
				->get();

			// Categories
			$categoriesQuery = VenueCategory::query();
			foreach ($tokens as $token) {
				$categoriesQuery->orWhere('name', 'like', "%{$token}%");
			}
			$categories = $categoriesQuery->take(5)->get();
		} else {
			// Just categories
			$categories = VenueCategory::take(5)->get();
		}

		// Prepare suggestions (categories first)
		foreach ($categories as $c) {
			array_push($suggestions, [
				'type' => 'category',
				'id' => $c->id,
				'name' => $c->name
			]);
		}
		foreach ($venues as $v) {
			array_push($suggestions, [
				'type' => 'venue',
				'id' => $v->id,
				'name' => $v->name,
				'category' => $v->categories()->first()->name,
				'city' => $v->address_city,
				'url' => route('site.venues.detail', ['venue' => $v])
			]);
		}

		return $suggestions;
	}
}