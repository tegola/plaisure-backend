<?php

namespace App\Importers;

use App\Models\VenueImport;

class Megabet extends Importer
{
	/**
	 * The brand name.
	 * 
	 * @var string
	 */
	protected $brand = 'Megabet';

	/**
	 * The brand to use for creating Venue imports.
	 * 
	 * @var integer
	 */
	protected $venueImportBrand = VenueImport::SOURCE_BRAND_MEGABET;

	/**
	 * Load data from the source.
	 * 
	 * @return void
	 */
	public function load()
	{
		// Get the data
		$crawler = $this->browser->request('GET', 'http://www.megabet.co.uk/p/shop-locator/');
		$tempData = [];

		// Loop through venue rows
		$crawler->filter('#RightContainer .table tbody tr')->each(function($tr) use (&$tempData) {
			$row = new \stdClass();

			// Loop through cells of each row
			$tr->filter('td')->each(function($td) use (&$row) {
				$tdClass = $td->attr('class');

				if ($tdClass == 'shopName') {
					$row->name = $td->text();
				} else if ($tdClass == 'shopAddress') {
					$row->address = $td->text();
				} else if ($tdClass == 'shopPostcode') {
					$row->postcode = $td->text();
				} else if ($tdClass == 'shopMap') {
					$td->filter('.shopLink')->each(function($link) use (&$row) {
						$href = $link->attr('href');
						$geoDataString = explode('Current+Location/', $href)[1];
						$geoDataArray = explode(',', $geoDataString);
						$row->latitude = (float) $geoDataArray[0];
						$row->longitude = (float) $geoDataArray[1];
					});
				}
			});

			// Generate an id for the row
			$row->generated_id = substr(md5($row->name . $row->postcode), 0, 8);

			array_push($tempData, $row);
		});

		$this->data = $tempData;
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
	 * Get a textual representation for the specified item.
	 *
	 * @param  \stdClass $item
	 * @return string
	 */
	public function getDescriptionForItem(\stdClass $item)
	{
		return "{$item->name}, {$item->address}, {$item->postcode}";
	}

	/**
	 * Normalize source item data for venue creation usage.
	 * 
	 * @param  \stdClass $item
	 * @return \stdClass
	 */
	public function normalizeItem(\stdClass $item)
	{
		return (object) [
			'name' => 'Megabet',
			'address_postcode' => $item->postcode,
			'country' => 'GB',
			'geo_latitude' => round($item->latitude, 6),
			'geo_longitude' => round($item->longitude, 6),
			'categories' => [
				['machine_name' => 'betting_shop', 'is_primary' => true]
			]
		];
	}
}