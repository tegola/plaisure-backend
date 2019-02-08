<?php

namespace App\Importers;

use App\Models\VenueImport;

class Cashino extends Importer
{
	/**
	 * The brand name.
	 * 
	 * @var string
	 */
	protected $brand = 'Cashino';

	/**
	 * The brand to use for creating Venue imports.
	 * 
	 * @var integer
	 */
	protected $venueImportBrand = VenueImport::SOURCE_BRAND_CASHINO;

	/**
	 * Load data from the source.
	 * 
	 * @return void
	 */
	public function load()
	{
		$response = $this->client->get('https://venues.cashino.com/venues.json');
		$this->data = json_decode($response->getBody());
	}

	/**
	 * Get the key to retrieve the unique venue id in each data row.
	 * 
	 * @return string
	 */
	public function getIdKey()
	{
		return 'Venue ID';
	}

	/**
	 * Get a textual representation for the specified item.
	 *
	 * @param  \stdClass $item
	 * @return string
	 */
	public function getDescriptionForItem(\stdClass $item)
	{
		return "{$item->{'Venue Name'}}, {$item->{'Venue Address'}}, {$item->{'Venue Town'}}";
	}

	/**
	 * Normalize source item data for venue creation usage.
	 * 
	 * @param  \stdClass $item
	 * @return \stdClass
	 */
	public function normalizeItem(\stdClass $item)
	{
		// Cleanup address (remove town)
		$address = str_replace($item->{'Venue Town'}, '', $item->{'Venue Address'});
		$address = trim($address);
		$address = preg_replace('/,$/', '', $address);

		// Find coords
		$coords = explode(',', $item->{'Coordinates'});

		// Find categories
		$categories = [];
		if ($item->{'AGC Venue'}) $categories[] = 'Adult Gaming Centre';
		if ($item->{'Bingo Venue'}) $categories[] = 'Bingo';
		if ($item->{'Bingo Express'}) $categories[] = 'Bingo Express';
		if ($item->{'Bingo Plus'}) $categories[] = 'Bingo Plus';
		if ($item->{'Cash Bingo'}) $categories[] = 'Cash Bingo';
		if ($item->{'FEC Venue'}) $categories[] = 'Family Entertainment Centre';

		// FIXME: Trovare gli orari

		return (object) [
			'name' => $item->{'Venue Name'},
			'address_line1' => $address,
			'address_city' => $item->{'Venue Town'},
			'address_postcode' => $item->{'Post Code'},
			'country' => 'GB',
			'geo_latitude' => (float) $coords[0],
			'geo_longitude' => (float) $coords[1],
			'contact_phone' => $item->{'Tel Number'},
			'categories' => $categories
		];
	}
}