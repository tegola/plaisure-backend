<?php

namespace App\Import\Importers;

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
}