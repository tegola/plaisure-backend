<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;
use App;

class SeoController extends Controller
{
	/**
	 * Build the sitemap.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function sitemap()
	{
		$sitemap = App::make("sitemap");

		// Set cache duration in minutes
		$sitemap->setCache('laravel.sitemap', 60);

		// Build if not cached
		if (!$sitemap->isCached()) {
			// Home page
			$sitemap->add(route('site.home'), null, '1.0', 'weekly');

			// Venues
			$venues = Venue::all();

			foreach ($venues as $venue) {
				$sitemap->add(route('site.venues.detail', ['venue' => $venue]), $venue->updated_at, 0.9, 'daily');
			}

			// About
			$sitemap->add(route('site.about'), null, '0.9', 'monthly');

			// Promote
			$sitemap->add(route('site.promote'), null, '0.9', 'weekly');
			
			// Play responsibly
			$sitemap->add(route('site.about'), null, '0.9', 'weekly');
		}

		// Generate XML
		return $sitemap->render('xml', null);
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
