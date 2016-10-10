<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

class Venue extends Model
{
	const SURFACE_TO_MACHINE_MULTIPLIER = 0.15;
	const MACHINE_TYPES = array('A', 'B', 'A/B');

	/**
	 * Get the estimated number of machines based on surface size
	 *
	 * @return integer  The estimated number
	 */
	public function getEstimatedMachineNumberAttribute()
	{
		if (!$this->machine_number && $this->surface_size) {
			return round($this->surface_size * self::SURFACE_TO_MACHINE_MULTIPLIER);
		} else {
			return 0;
		}
	}

	/**
	 * Returns whether the machine number has been faked
	 *
	 * @return boolean
	 */
	public function hasFakeMachineNumber()
	{
		return $this->machine_number === 0;
	}

	/**
	 * Categories the venue is in
	 *
	 * @return App\Category
	 */
	public function categories()
	{
		return $this->belongsToMany('App\Category');
	}

	/**
	 * Get the short address
	 *
	 * @return string
	 */
	public function getShortAddressAttribute()
	{
		return "{$this->address_street} {$this->address_number}, {$this->address_city }";
	}

	/**
	 * Get the long address
	 *
	 * @return string
	 */
	public function getLongAddressAttribute()
	{
		return "{$this->address_street} {$this->address_number}, {$this->address_postcode} {$this->address_city } {$this->address_region}, {$this->address_country}";
	}

	/**
	 * Get the distance in readable format
	 *
	 * 0.8123 becomes 800 m
	 * 1.2455 becomes 1.2 km
	 * 10.245 becomes 10 km
	 *
	 * @return string Distance in meters or kilometers
	 */
	// FIXME: Move to a Helper
	public function getFormattedDistanceAttribute()
	{
		if (!$this->distance) {
			return;
		}

		if ($this->distance > 10) {
			return round($this->distance) . ' km';
		}
		if ($this->distance > 1) {
			return round($this->distance, 1) . ' km';
		}
		if ($this->distance < 1) {
			return round($this->distance * 100) . ' m';
		}
	}


	/**
	 * Get the file icon name for the first venue category
	 * 
	 * @return String The file name
	 */
	// FIXME: Move to a Helper / Refactor
	public function getCategoryIconNameAttribute()
	{
		$file_name = '';

		if ($this->categories()->count()) {
			switch ($this->categories()->first()->name) {
				case 'Agenzia scommesse':
					$file_name = 'token.svg';
					break;
				case 'Ricevitoria':
					$file_name = 'receipt.svg';
					break;
				case 'Sala Bingo':
					$file_name = 'bingo.svg';
					break;
				case 'Sala VLT':
					$file_name = 'slot-machine.svg';
					break;
			}
		}

		return $file_name;
	}

	/**
	 * Query builder scope to list neighboring locations
	 * within a given distance from a given location
	 * https://gist.github.com/stevenmaguire/3ada3f73f1ad03356cf5
	 *
	 * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
	 * @param  mixed                              $lat    Lattitude of given location
	 * @param  mixed                              $lng    Longitude of given location
	 * @param  integer                            $radius Optional distance
	 * @param  string                             $unit   Optional unit
	 *
	 * @return Illuminate\Database\Query\Builder          Modified query builder
	 */
	public function scopeNear($query, $lat, $lng, $radius = 100, $unit = "km")
	{
		$unit = ($unit === "km") ? 6378.10 : 3963.17;
		$lat = (float) $lat;
		$lng = (float) $lng;
		$radius = (double) $radius;
		$lat_column = 'geo_latitude';
		$lng_column = 'geo_longitude';

		return $query->having('distance','<=',$radius)
			->select(DB::raw("*,
				 ($unit * ACOS(COS(RADIANS($lat))
						* COS(RADIANS($lat_column))
						* COS(RADIANS($lng) - RADIANS($lng_column))
						+ SIN(RADIANS($lat))
						* SIN(RADIANS($lat_column)))) AS distance")
			)->orderBy('distance','asc');
	}

	public function scopeWithNameOrCategory($query, $name)
	{
		return $query
			->where('name', 'like', "%{$name}%") // Venue name
			->orWhereHas('categories', function($query) use ($name){ // Category name
				$query->where('name', 'like', "%{$name}%");
			});
	}
}
