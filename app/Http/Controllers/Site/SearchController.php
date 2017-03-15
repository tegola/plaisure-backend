<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;

use App\Venue;
use App\Category;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
	public function suggestions(Request $request)
	{
		$what = trim($request->what);
		$c_lat = $request->has('c_lat') ? floatval($request->c_lat) : null;
		$c_lng = $request->has('c_lng') ? floatval($request->c_lng) : null;
		$near = $request->near;
		$venues = [];
		$categories = [];
		$suggestions = [];

		// Find venues and categories
		if ($what) {
			if ($c_lat && $c_lng) {
				$venues = Venue::with('categories')->withNameOrCategoryName($what);
				$venues = $venues->near($c_lat, $c_lng, config('constants.search_default_distance'));
				$venues = $venues->take(5)->get();
			}
			$categories = Category::where('name', 'like', "%{$what}%")->take(5)->get();
		} else {
			$categories = Category::take(5)->get();
		}

		// Prepare suggestions (categories first)
		foreach ($categories as $c) {
			array_push($suggestions, [
				"type" => "category",
				"id" => $c->id,
				"name" => $c->name
			]);
		}
		foreach ($venues as $v) {
			array_push($suggestions, [
				"type" => "venue",
				"id" => $v->id,
				"name" => $v->name,
				"category" => $v->categories()->first()->name,
				"city" => $v->address_city,
				"url" => route('site.venues.detail', ['venue' => $v])
			]);
		}

		return $suggestions;
	}
}