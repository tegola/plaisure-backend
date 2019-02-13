<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VenueImport extends Model
{
	use SoftDeletes;

	const SOURCE_BRAND_ADMIRAL_UK       = 1;
	const SOURCE_BRAND_CASHINO          = 2;
	const SOURCE_BRAND_MEGABET          = 3;
	const SOURCE_BRAND_LADBROKES        = 4;
	const SOURCE_BRAND_WILLIAM_HILL_UK  = 5;
	
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'source_data' => 'object',
		'normalized_data' => 'object'
	];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	/**
	 * Checks whether the import has the minimum requirements to create a new
	 * venue.
	 * 
	 * @return boolean
	 */
	public function isReadyForVenue()
	{
		$data = (array) $this->normalized_data;
		$requiredKeys = [
			'name',
			'categories',
			'address_line1',
			'address_city',
			'address_postcode',
			'country',
			'geo_latitude',
			'geo_longitude'
		];
		$isReady = true;

		foreach ($requiredKeys as $key) {
			if (!array_key_exists($key, $data) || !$data[$key]) {
				$isReady = false;
			}
		}

		return $isReady;
	}

	/**
	 * Get readable source brand name.
	 * 
	 * @return string
	 */
	public function readableSourceBrand()
	{
		switch ($this->source_brand) {
			case self::SOURCE_BRAND_ADMIRAL_UK: return 'Admiral UK';
			case self::SOURCE_BRAND_CASHINO: return 'Cashino';
			case self::SOURCE_BRAND_MEGABET: return 'Megabet';
			case self::SOURCE_BRAND_LADBROKES: return 'Ladbrokes';
			case self::SOURCE_BRAND_WILLIAM_HILL_UK: return 'William Hill UK';
		}
		
	}

	/**
	 * Venue that get their data from this import.
	 *
	 * @return [App\Models\Venue]
	 */
	public function venues()
	{
		return $this->hasMany('App\Models\Venue');
	}
}
