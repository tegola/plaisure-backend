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
	 * Whether the importer does one request per venue.
	 * 
	 * @var boolean
	 */
	protected $cycles = false;

	/**
	 * The current fetch index, for importers that make multiple requests.
	 * 
	 * @var integer
	 */
	private $index = 1;

	/**
	 * Fetched data.
	 * 
	 * @var array
	 */
	private $data = [];

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
	 * Whether the importer has more data to fetch.
	 * 
	 * @var boolean
	 */
	private $hasMore = true;

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
	 * Fetches the importer data and appends it to the stored data.
	 * 
	 * @return void
	 */
	public function load()
	{
		// Fetch
		$rows = $this->fetch();

		// If there are new rows, append them to data
		if ($rows) {
			$this->data = array_merge($this->data, $rows);
		}

		// Set next index
		$this->index++;
	}

	/**
	 * Fetch data from the source.
	 * 
	 * @return [stdClass]|null
	 */
	abstract public function fetch();

	/**
	 * Get ids (real or generated) from fetched data.
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
	 * Generate an id hash for the specified string.
	 * 
	 * @param  string $bits
	 * @return string
	 */
	protected function generateId(string $bits)
	{
		return substr(md5($bits), 0, 8);
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
	 * Stop fetching after the current cycle.
	 * 
	 * @return void
	 */
	protected function end()
	{
		$this->hasMore = false;
	}

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
	 * Get wheter the importer does one request per venue.
	 * 
	 * @var boolean
	 */
	public function cycles()
	{
		return $this->cycles;
	}

	/**
	 * Set the fetch index.
	 * 
	 * @param int $index
	 */
	public function setIndex(int $index) {
		$this->index = $index;
	}

	/**
	 * Get the current fetch index.
	 * 
	 * @return integer
	 */
	public function getIndex()
	{
		// "+ 1" to take automatic increment into account
		return $this->index;
	}

	/**
	 * Get the fetched data.
	 * 
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Determines if the importer has fetched all data.
	 * 
	 * @return array
	 */
	public function hasMore()
	{
		return $this->hasMore;
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