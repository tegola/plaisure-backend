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

		// Set cache duration
		$sitemap->setCache('laravel.sitemap', now()->addHours(12));

		// Build if not cached
		if (!$sitemap->isCached()) {
			// Home page
			$sitemap->add(route('home'), null, '1.0', 'weekly', [], null, $this->sitemapTranslatedUrls('home'));

			// Venues
			foreach (Venue::all() as $venue) {
				$name = 'venues.detail';
				$params = compact('venue');
				$sitemap->add(route($name, $params), $venue->updated_at, 0.9, 'daily', [], null, $this->sitemapTranslatedUrls($name, $params));
			}

			// About, Promote
			$sitemap->add(route('about'), null, 0.9, 'monthly', [], null, $this->sitemapTranslatedUrls('about'));
			$sitemap->add(route('promote'), null, 0.9, 'weekly', [], null, $this->sitemapTranslatedUrls('promote'));
			$sitemap->add(route('play-responsibly'), null, 0.9, 'weekly', [], null, $this->sitemapTranslatedUrls('play-responsibly'));
		}

		// Generate XML
		return $sitemap->render('xml');
	}

	/**
	 * Build the translated urls of the specified route for all supported
	 * languages.
	 *
	 * @param  string $name
	 * @param  array  $params
	 * @return array
	 */
	private function sitemapTranslatedUrls(string $name, $params = [])
	{
		$additionalLocales = ['it'];
		$urls = [];

		foreach ($additionalLocales as $locale) {
			$urls[] = [
				'language' => $locale,
				'url' => route($name, array_merge(['locale' => $locale], $params))
			];
		}

		return $urls;
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
