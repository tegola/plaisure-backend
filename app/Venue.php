<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

class Venue extends Model
{
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
}
