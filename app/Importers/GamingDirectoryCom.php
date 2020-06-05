<?php

namespace App\Importers;

use App\Models\VenueImport;

class GamingDirectoryCom extends Importer
{
	/**
	 * The brand name.
	 *
	 * @var string
	 */
	protected $brand = 'GamingDirectory.com';

	/**
	 * The list of searchable countries for getting the two-digit code.
	 *
	 * @var array
	 */
	private $countries = [];

	/**
	 * The brand to use for creating Venue imports.
	 *
	 * @var integer
	 */
	protected $venueImportBrand = VenueImport::SOURCE_BRAND_GAMING_DIRECTORY_COM;

	public function __construct()
	{
		parent::__construct();

		// Prepare the searchable country list with "and" instead of "&"
		$countries = require base_path('vendor/umpirsky/country-list/data/en_US/country.php');
		$countries = array_flip($countries);

		$this->countries = $countries;
	}

	/**
	 * Load data from the source.
	 *
	 * @return void
	 */
	public function fetch()
	{
		$file = fopen(storage_path('gaming_directory_com.csv'), 'r');
		$line = 0;
		$keys = [];
		$rows = [];

		while (($data = fgetcsv($file, 0, ';')) !== false) {
			$line++;

			// Store field names
			if ($line === 1) {
				$keys = $data;
				continue;
			}

			// Get row as keys => values
			$row = array_combine($keys, $data);

			$rows[] = json_decode(json_encode($row));
		}

		// Mark as ended
		$this->end();

		return $rows;
	}

	/**
	 * Get the key to retrieve the unique venue id in each data row.
	 *
	 * @return string
	 */
	public function getIdKey()
	{
		return 'PropertyId';
	}

	/**
	 * Get a textual representation for the specified item.
	 *
	 * @param  \stdClass $item
	 * @return string
	 */
	public function getDescriptionForItem(\stdClass $item)
	{
		return "{$item->Company}, {$item->PhysicalAddress1}, {$item->PhysicalCity}";
	}

	/**
	 * Normalize source item data for venue creation usage.
	 *
	 * @param  \stdClass $item
	 * @return \stdClass
	 */
	public function normalizeItem(\stdClass $item)
	{
		$data = [];

		// Don't even normalize if it's not open
		if ($item->PropertyStatus !== 'Open') {
			return (object) $data;
		}

		// Init basic data
		$data['name'] = $item->Company;
		$data['address_line1'] = $item->PhysicalAddress1;
		$data['address_line2'] = $item->PhysicalAddress2;
		$data['address_city'] = $item->PhysicalCity;
		$data['address_postcode'] = $item->PhysicalPostalCode;
		$data['address_region'] = $item->StateLong;
		$data['parking_capacity'] = $item->ParkingSpaces;
		$data['contact_phone'] = $item->Phone;
		$data['contact_email'] = $item->Email;

		// Surface size (with sq. feet to sq. meters conversion)
		if ($item->CasinoSquareFootage) {
			$data['surface_size'] = $item->CasinoSquareFootage * 0.092903;
		}

		// Find country
		$country = trim($item->PhysicalCountry);

		// Adjust to better match the country list
		$country = str_replace(' & ', ' and ', $country);

		// Handle specific error cases
		if ($country === 'Viet Nam') {
			$country = 'Vietnam';
		}

		$data['country'] = data_get($this->countries, $country);

		// Find coords (only when mostly precise)
		if (in_array($item->LocationPrecision, ['Street', 'Building']) && $item->Latitude && $item->Longitude) {
			$data['geo_latitude'] = (float) $item->Latitude;
			$data['geo_longitude'] = (float) $item->Longitude;
		}

		// Find categories
		$categories = [];

		switch ($item->PropertyType) {
			case 'Betting Shop':
			case 'Off-Track Betting Facility':
				$categories[] = ['machine_name' => 'betting_shop', 'is_primary' => true];
				break;
			case 'Bingo Hall':
				$categories[] = ['machine_name' => 'bingo', 'is_primary' => true];
				break;
			case 'Card Room':
				$categories[] = ['machine_name' => 'card_room', 'is_primary' => true];
				break;
			case 'Casino':
				$categories[] = ['machine_name' => 'casino', 'is_primary' => true];
				break;
			// case 'Casino Cruise':
				// $categories[] = ['machine_name' => 'casino'];
				// break;
			// case 'Cruise Ship':
				// $categories[] = ['machine_name' => 'casino'];
				// break;
			// case 'Dog Track':
			// case 'Dog Track Racino':
			// case 'Horse Track':
			// case 'Horse Track Racino':
				// $categories[] = [];
				// break;
			// case 'Jai-Alai':
				// $categories[] = [];
				// break;
		}

		if ($item->Slots || $item->TableGames) {
			$categories[] = ['machine_name' => 'casino'];
		}
		if ($item->PokerTables) {
			$categories[] = ['machine_name' => 'card_room'];
		}
		if ($item->BingoSeats) {
			$categories[] = ['machine_name' => 'bingo'];
		}
		if ($item->Sportsbook === 'Yes' || $item->Racebook === 'Yes') {
			$categories[] = ['machine_name' => 'betting_shop'];
		}

		// Make categories unique. Primary categories stay at first position so
		// they're the one that will be kept
		if (count($categories) > 1) {
			$categories = collect($categories)->unique('machine_name')->values();
		}

		$data['categories'] = $categories;

		// VLT/AWP slots (just slots here)
		if ($item->Slots) {
			$data['vlt_machine_count'] = $item->Slots;
		}

		// Sports betting
		if ($item->Sportsbook === 'Yes') {
			$data['sports_betting'] = true;
		}

		// Horse betting
		if (in_array($item->PropertyType, ['Horse Track', 'Horse Track Racino'])) {
			$data['horse_betting'] = true;
		}

		// Website or facebook url
		if ($website = $item->WebSite) {
			if (stripos($website, 'facebook.com') !== false) {
				$data['url_facebook'] = $website;
			} else {
				$data['url_site'] = $website;
			}
		}

		// Sort data by key
		ksort($data);

		return (object) $data;
	}
}
