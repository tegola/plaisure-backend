<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;

use App\Models\Venue;
use App\Models\Category;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
	/**
	 * Show the home page.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('site.home');
	}

	/**
	 * Prepare category and venue suggestions, the latter only if location data
	 * is present.
	 * 
	 * @param  Request $request
	 * @return array
	 */
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
				$venues = Venue::with('categories')
					->withNameOrCategoryName($what)
					->withDistanceFrom($c_lat, $c_lng)
					->take(5)
					->get();
			}
			$categories = Category::where('name', 'like', "%{$what}%")
				->take(5)
				->get();
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