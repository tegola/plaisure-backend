<?php

namespace App\Importers;

use App\Models\VenueImport;

class Ladbrokes extends Importer
{
	/**
	 * The brand name.
	 * 
	 * @var string
	 */
	protected $brand = 'Ladbrokes';

	/**
	 * The brand to use for creating Venue imports.
	 * 
	 * @var integer
	 */
	protected $venueImportBrand = VenueImport::SOURCE_BRAND_LADBROKES;

	/**
	 * Load data from the source.
	 * 
	 * @return void
	 */
	public function load()
	{
		// https://viewer.blipstar.com/searchdbnew?uid=2470030&lat=51.494506&lng=-0.099973&value=50000&max=50000

		// Get the data
		$tempData = [];
		$centers = [
			[57.4680424, -4.2919821],
			[55.5269664, -3.3482706],
			[55.1928772, -2.7141486],
			[52.337128, -0.074645],
			[51.839323, -2.734778]
		];

		foreach ($centers as $coords) {
			$response = $this->client->get('https://viewer.blipstar.com/searchdbnew', [
				'query' => [
					'uid' => 2470030,
					'lat' => $coords[0],
					'lng' => $coords[1],
					'value' => 1000, // Max allowed
					'max' => 1000 // Max allowed
				]
			]);

			// Concatenate to data
			$responseData = json_decode($response->getBody());
			array_shift($responseData); // Remove totals
			$tempData = array_merge($tempData, $responseData);
		}

		// Generate an id for each row
		foreach ($tempData as $row) {
			$row->generated_id = substr(md5($row->n . $row->pc), 0, 8);
		}

		// Find unique venues by filtering their generated id
		$uniqueIds = [];
		$tempData = array_filter($tempData, function($row) use (&$uniqueIds) {
			if (!in_array($row->generated_id, $uniqueIds)) {
				$uniqueIds[] = $row->generated_id;
				return $row;
			}
		});

		// Store data
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
		return "{$item->n}, {$item->ad}";
	}

	/**
	 * Normalize source item data for venue creation usage.
	 * 
	 * @param  \stdClass $item
	 * @return \stdClass
	 */
	public function normalizeItem(\stdClass $item)
	{
		// Find address
		$address = explode(',', $item->ad);
		$address_line1 = '';
		$address_city = '';

		foreach ($address as $index => $component) {
			$component = trim($component);

			if ($component != $item->pc && $component == strtoupper($component)) {

				// City
				$address_city = trim($component);
				unset($address[$index]);

			} else if ($component == $item->pc) {

				// Post code
				unset($address[$index]);

			}
		}

		$address_line1 = implode(',', $address);

		// Find business hours
		$daysKeys = [
			1 => 'mon',
			2 => 'tue',
			3 => 'wed',
			4 => 'thu',
			5 => 'fri',
			6 => 'sat',
			0 => 'sun'
		];
		$business_hours = [];

		foreach ($daysKeys as $day => $name) {
			// Skip day if empty
			if (!$item->$name) continue;

			$hours = explode('-', $item->$name);
			$opens = date('H:i', strtotime(trim($hours[0])));
			$closes = date('H:i', strtotime(trim($hours[1])));

			$business_hours[] = compact('day', 'opens', 'closes');
		}

		return (object) [
			'name' => $item->n,
			'address_line1' => $address_line1,
			'address_city' => $address_city,
			'address_postcode' => $item->pc,
			'country' => 'GB',
			'geo_latitude' => round($item->lat, 6),
			'geo_longitude' => round($item->lng, 6),
			'business_hours' => $business_hours,
			'categories' => [
				['machine_name' => 'betting_shop', 'is_primary' => true]
			]
		];
	}
}