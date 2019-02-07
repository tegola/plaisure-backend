<?php

namespace App\Import\Importers;

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
}