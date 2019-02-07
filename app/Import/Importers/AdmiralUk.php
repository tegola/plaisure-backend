<?php

namespace App\Import\Importers;

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
}