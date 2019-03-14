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
		$sitemap = App::make('sitemap');
		$frontendUrl = env('FRONTEND_URL');

		// Set cache duration
		$sitemap->setCache('laravel.sitemap', now()->addHours(12));

		// Build if not cached
		if (!$sitemap->isCached()) {
			// Home page
			$sitemap->add($frontendUrl, null, '1.0', 'weekly');

			// Venues
			$venues = Venue::all();
			foreach ($venues as $venue) {
				$sitemap->add("{$frontendUrl}/venues/{$venue->id_hashed}", $venue->updated_at, 0.9, 'daily');
			}

			// About, Promote, Play responsibly
			$sitemap->add("{$frontendUrl}/about", null, '0.9', 'monthly');
			$sitemap->add("{$frontendUrl}/promote", null, '0.9', 'weekly');
			$sitemap->add("{$frontendUrl}/play-responsibly", null, '0.9', 'weekly');
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
		$sitemapUrl = url('/sitemap.xml');
		$lines = [
			"User-agent: *",
			"Sitemap: {$sitemapUrl}",
			"Allow: {$sitemapUrl}",
			"Disallow: /"
		];

		$text = implode(PHP_EOL, $lines);

		return response($text, 200, ['Content-Type' => 'text/plain']);
	}
}
