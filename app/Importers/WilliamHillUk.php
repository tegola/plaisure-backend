<?php

namespace App\Importers;

use App\Models\VenueImport;

class WilliamHillUk extends Importer
{
	/**
	 * The brand name.
	 * 
	 * @var string
	 */
	protected $brand = 'William Hill UK';

	/**
	 * The brand to use for creating Venue imports.
	 * 
	 * @var integer
	 */
	protected $venueImportBrand = VenueImport::SOURCE_BRAND_WILLIAM_HILL_UK;

	/**
	 * Whether the importer does one request per venue.
	 * 
	 * @var boolean
	 */
	protected $cycles = true;

	/**
	 * Fetch data from the source.
	 * 
	 * @return [stdClass]|null
	 */
	public function fetch()
	{
		// Get the page
		$crawler = $this->browser->request('GET', "http://shoplocator.williamhill/results/current_store/{$this->getIndex()}");

		// Stop if last request didn't work
		if ($this->browser->getResponse()->getStatus() !== 200) {
			return $this->end();
		}

		// Init row
		$row = new \stdClass();
		$row->name = 'William Hill';

		// Get address and business hours
		$crawler->filter('[itemprop]')->each(function($node) use (&$row) {
			$prop = $node->attr('itemprop');

			if ($prop == 'streetAddress') {
				$row->address = $node->text();
			} else if ($prop == 'addressLocality') {
				$row->city = preg_replace('/,$/', '', $node->text());
			} else if ($prop == 'postalCode') {
				$row->postcode = $node->text();
			} else if ($prop == 'telephone') {
				$row->telephone = $node->text();
			} else if ($prop == 'email') {
				$row->email = $node->text();
			} else if ($prop == 'openingHours') {
				$row->hours[$node->attr('datetime')] = $node->text();
			}
		});

		// Get geo data
		$crawler->filter('[data-target]')->each(function($node) use (&$row) {
			list($latitude, $longitude) = explode('|', $node->attr('data-target'));

			$row->latitude = $latitude;
			$row->longitude = $longitude;
		});

		// Get facilities
		$crawler->filter('.p')->each(function($node) use (&$row) {
			$text = $node->text();

			if (strpos($text, 'Facilities:') !== false) {
				$text = preg_replace("/\r|\n/", '', $text);
				$text = str_replace("Facilities:", '', $text);
				$text = trim($text);

				$row->facilities = $text;
			};
		});

		// Generate an id for the row
		$row->generated_id = $this->generateId($row->name . $row->postcode);

		// Recursive casting as object
		$row = json_decode(json_encode($row));

		return [$row];
	}

	/**
	 * Get the key to retrieve the unique venue id in each data row.
	 * 
	 * @return string
	 */
	public function getIdKey()
	{
		return 'generated_id';
	}

	/**
	 * Normalize source item data for venue creation usage.
	 * 
	 * @param  \stdClass $item
	 * @return \stdClass
	 */
	public function normalizeItem(\stdClass $item)
	{
		// Prepare categories
		$categories = array_map(function($facility) {
			$category = [];

			switch (trim($facility)) {
				case 'Gaming machines':
					$category['machine_name'] = 'adult_gaming_center';
					break;
				case 'WH Self-service betting terminals':
					$category['machine_name'] = 'betting_shop';
					$category['primary'] = 'true';
					break;
			}

			return $category;
		}, explode(',', $item->facilities));

		// Force a single category to be primary
		if (count($categories) == 1) $categories[0]['primary'] = true;

		return (object) [
			'name' => 'William Hill',
			'address_line1' => $item->address,
			'address_city' => $item->city,
			'address_postcode' => $item->postcode,
			'country' => 'GB',
			'geo_latitude' => round($item->latitude, 6),
			'geo_longitude' => round($item->longitude, 6),
			'categories' => $categories
		];
	}
}