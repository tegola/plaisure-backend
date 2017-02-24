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
		$lat = $request->lat;
		$lng = $request->lng;
		$near = $request->near;
		$venues = [];
		$categories = [];
		$suggestions = [];

		// Find venues and categories
		if ($what) {
			$venues = Venue::with('categories')->withNameOrCategory($what);
			if ($lat && $lng) {
				$venues = $venues->near($lat, $lng, 20);
			}
			$venues = $venues->take(5)->get();
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
				"city" => $v->address_city
			]);
		}

		return response()->json($suggestions);
	}
}