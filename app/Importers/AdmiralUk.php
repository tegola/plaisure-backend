<?php

namespace App\Importers;

use App\Models\VenueImport;

class AdmiralUk extends Importer
{
	/**
	 * The brand name.
	 * 
	 * @var string
	 */
	protected $brand = 'Admiral UK';

	/**
	 * The brand to use for creating Venue imports.
	 * 
	 * @var integer
	 */
	protected $venueImportBrand = VenueImport::SOURCE_BRAND_ADMIRAL_UK;

	/**
	 * Load data from the source.
	 * 
	 * @return void
	 */
	public function load()
	{
		$response = $this->client->get('https://www.admiralslots.co.uk/venues.json');
		$this->data = json_decode($response->getBody());
	}

	/**
	 * Normalize source item data for venue creation usage.
	 * 
	 * @param  \stdClass $item
	 * @return \stdClass
	 */
	public function normalizeItem(\stdClass $item)
	{
		// Find city and province
		$cityAndProvince = explode(',', $item->city);

		return (object) [
			'name' => $item->name,
			'address_line1' => $item->address,
			'address_city' => trim($cityAndProvince[0]),
			'address_postcode' => $item->postcode,
			'address_province' => count($cityAndProvince) > 1 ? trim($cityAndProvince[1]) : '',
			'country' => 'GB',
			'geo_latitude' => round($item->lat, 6),
			'geo_longitude' => round($item->lng, 6),
			'contact_phone' => $item->telephone,
			'url_site' => "https://www.admiralslots.co.uk/venue/{$item->link}/"
		];
	}
}