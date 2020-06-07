<?php

namespace App\Http\Controllers;

class SeoController extends Controller
{
	/**
	 * Build the robots.txt file.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function robots()
	{
		$sitemapUrl = url('/sitemap.xml');
		$lines = [
			'User-agent: *',
			"Sitemap: {$sitemapUrl}",
			"Allow: {$sitemapUrl}",
			"Disallow: /"
		];

		$text = implode(PHP_EOL, $lines);

		return response($text, 200, ['Content-Type' => 'text/plain']);
	}
}
