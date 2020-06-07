<?php

namespace App\Console\Commands;

use App\Models\Venue;
use DB;
use File;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.xml file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		File::makeDirectory(public_path('sitemaps'));

		// Make the sitemap index
		$sitemap = app()->make('sitemap');

		// Get distinct countries
		$countries = DB::table('venues')
			->distinct()
			->select('country')
			->where('country', '!=', '')
			->orderBy('country')
			->pluck('country');

		// Build the sitemap for the basic pages
		$baseSitemap = app()->make('sitemap');
		$baseSitemap->add(route('home'), null, '1.0', 'weekly', [], null, $this->makeTranslatedUrls('home'));
		$baseSitemap->add(route('about'), null, 0.9, 'monthly', [], null, $this->makeTranslatedUrls('about'));
		$baseSitemap->add(route('promote'), null, 0.9, 'weekly', [], null, $this->makeTranslatedUrls('promote'));
		$baseSitemap->add(route('play-responsibly'), null, 0.9, 'weekly', [], null, $this->makeTranslatedUrls('play-responsibly'));
		$baseSitemap->store('xml', 'base', public_path('sitemaps'));

		$sitemap->addSitemap(url()->to('sitemaps/base.xml'));

		// Build sitemaps for venues in each country
		foreach ($countries as $country) {
			$country = strtolower($country);
			$countrySitemap = app()->make('sitemap');
			$venues = Venue::where('country', $country)->get();

			foreach ($venues as $venue) {
				$name = 'venues.detail';
				$params = compact('venue');
				$countrySitemap->add(route($name, $params), $venue->updated_at, 0.9, 'daily', [], null, $this->makeTranslatedUrls($name, $params));
			}

			$countrySitemap->store('xml', $country, public_path('sitemaps'));
			$sitemap->addSitemap(url()->to("sitemaps/{$country}.xml"));
		}

		// Store
		return $sitemap->store('sitemapindex');
	}

	/**
	 * Build the translated urls of the specified route for all supported
	 * languages.
	 *
	 * @param  string $name
	 * @param  array  $params
	 * @return array
	 */
	private function makeTranslatedUrls(string $name, $params = [])
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
}
