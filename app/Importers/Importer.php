<?php

namespace App\Importers;

use GuzzleHttp\Client;
use Goutte\Client as Browser;

abstract class Importer
{
	/**
	 * The brand name.
	 * 
	 * @var string
	 */
	protected $brand;

	/**
	 * The brand to use for creating VenueImport models.
	 * 
	 * @var integer
	 */
	protected $venueImportBrand;

	/**
	 * Downloaded data.
	 * 
	 * @var array
	 */
	protected $data = [];

	/**
	 * The Guzzle client.
	 * 
	 * @var Client
	 */
	protected $client;

	/**
	 * The Goutte client, called Browser because mimics a user using a browser.
	 * 
	 * @var Browser
	 */
	protected $browser;

	/**
	 * Create a new Importer instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->client = new Client();
		$this->browser = new Browser();
	}

	/**
	 * Load data from the source.
	 * 
	 * @return void
	 */
	abstract public function load();

	/**
	 * Get ids (real or generated) from downloaded data.
	 * 
	 * @return \Illuminate\Support\Collection
	 */
	public function getIds()
	{
		$idKey = $this->getIdKey();

		return collect($this->data)->map(function($item) use ($idKey) {
			return $item->$idKey;
		});
	}

	/**
	 * Get a textual representation for the specified item.
	 *
	 * @param  \stdClass $item
	 * @return string
	 */
	public function getDescriptionForItem(\stdClass $item)
	{
		return "{$item->name}, {$item->address}, {$item->city}";
	}

	/**
	 * Normalize source item data for venue creation usage.
	 * 
	 * @param  \stdClass $item
	 * @return \stdClass
	 */
	abstract public function normalizeItem(\stdClass $item);

	/**
	 * Get the Brand name.
	 * 
	 * @return string
	 */
	public function getBrand()
	{
		return $this->brand;
	}

	/**
	 * Get the Brand type for VenueImport models.
	 * 
	 * @return string
	 */
	public function getVenueImportBrand()
	{
		return $this->venueImportBrand;
	}

	/**
	 * Get the downloaded data.
	 * 
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Get the key to retrieve the unique venue id in each data row.
	 * 
	 * @return string
	 */
	public function getIdKey()
	{
		return 'id';
	}
}