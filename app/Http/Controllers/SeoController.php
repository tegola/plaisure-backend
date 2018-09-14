<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;
use App;

class SeoController extends Controller
{
	/**
	 * Redirect /venues/{id} to /venues/{id_hashed} or shows the 404 page.
	 * FIXME: Remove whene there are no more hits.
	 * 
	 * @param  int $id The venue id
	 * @return Illuminate\Http\Response
	 */
	public function redirectToHashed($id) {
		$venue = Venue::find($id);

		// Stop if venue doesn't exist
		abort_if(!$venue, 404);

		// Redirect to venue with hashed id
		return redirect("/venues/{$venue->id_hashed}", 301);
	}

	/**
	 * Build the sitemap.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function sitemap()
	{
		$sitemap = App::make('sitemap');

		// Set cache duration in minutes
		$sitemap->setCache('laravel.sitemap', 60);

		// Build if not cached
		if (!$sitemap->isCached()) {
			// Home page
			$sitemap->add(url('/'), null, '1.0', 'weekly');

			// Venues
			$venues = Venue::all();
			foreach ($venues as $venue) {
				$sitemap->add(url("/venues/{$venue->id_hashed}"), $venue->updated_at, 0.9, 'daily');
			}

			// About, Promote, Play responsibly
			$sitemap->add(url('/about'), null, '0.9', 'monthly');
			$sitemap->add(url('/promote'), null, '0.9', 'weekly');
			$sitemap->add(url('/play-responsibly'), null, '0.9', 'weekly');
		}

		// Generate XML
		return $sitemap->render('xml');
	}

	/**
	 * Build the robots.txt file.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function robots()
	{		
		$lines = [
			"User-agent: *",
			"Disallow: /admin"
		];

		if (App::environment('production')) {
			$sitemapUrl = url('/sitemap');
			array_push($lines, "Sitemap: {$sitemapUrl}");
		} else {
			array_push($lines, "Disallow: *");
		}

		$text = implode(PHP_EOL, $lines);

		return response($text, 200, ['Content-Type' => 'text/plain']);
	}
}
