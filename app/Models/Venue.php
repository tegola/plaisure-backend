<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Venue extends Model
{
	const SURFACE_TO_MACHINE_MULTIPLIER = 0.15;

	const MACHINE_TYPE_A  = 1;
	const MACHINE_TYPE_B  = 2;
	const MACHINE_TYPE_AB = 3;

	/**
	 * Default attributes. This is needed to pass the empty object to Vue's
	 * 'data' object, so it can be reactive. Setting a default on the migration
	 * does not prefill the model.
	 * 
	 * @var array
	 */
	protected $attributes = [
		'aams_census_code' => '',
		'aams_subject_enrollment_code' => '',

		'name' => '',
		'surface_size' => 0,
		'machine_count' => 0,
		'machine_type' => self::MACHINE_TYPE_A,

		'address_street' => '',
		'address_number' => '',
		'address_city' => '',
		'address_postcode' => '',
		'address_province' => '',
		'address_region' => '',
		'address_country' => '',

		'geo_latitude' => null,
		'geo_longitude' => null,

		'contact_phone' => '',
		'contact_email' => '',
		'contact_facebook' => '',
		'contact_twitter' => '',

		'url_site' => '',
		'url_facebook' => '',
		'url_tripadvisor' => ''
	];

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'aams_census_code',
		'aams_subject_enrollment_code',
		'name',
		'surface_size',
		'machine_type',
		'address_street',
		'address_number',
		'address_city',
		'address_postcode',
		'address_province',
		'address_region',
		'address_country',
		'geo_latitude',
		'geo_longitude',
		'contact_phone',
		'contact_email',
		'contact_facebook',
		'contact_twitter',
		'url_site',
		'url_facebook',
		'url_tripadvisor'
	];

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = [
		'short_address',
		'long_address',
		'category_icon_name'
	];


	/**
	 * By default, load all venue data on new queries.
	 * https://theokouzelis.com/php/laravel-eloquent-calculated-fields.html
	 * 
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function newQuery()
	{
		return parent::newQuery()->select('venues.*');
	}

	/**
	 * List of machine types.
	 * 
	 * @return array
	 */
	static function machineTypes()
	{
		return [
			self::MACHINE_TYPE_A => 'A',
			self::MACHINE_TYPE_B => 'B',
			self::MACHINE_TYPE_AB => 'A/B'
		];
	}

	/**
	 * Get the estimated number of machines based on surface size.
	 *
	 * @return integer  The estimated number
	 */
	public function getEstimatedMachineCountAttribute()
	{
		return $this->surface_size ? round($this->surface_size * self::SURFACE_TO_MACHINE_MULTIPLIER) : 0;
	}

	/**
	 * Get the short address.
	 *
	 * @return string
	 */
	public function getShortAddressAttribute()
	{
		return "{$this->address_street} {$this->address_number}, {$this->address_city }";
	}

	/**
	 * Get the long address.
	 *
	 * @return string
	 */
	public function getLongAddressAttribute()
	{
		return "{$this->address_street} {$this->address_number}, {$this->address_postcode} {$this->address_city } {$this->address_region}, {$this->address_country}";
	}

	/**
	 * Get the distance in readable format.
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
	 * Get the file icon name for the first venue category.
	 * 
	 * @return string
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
	 * Get the Google Maps URL.
	 * 
	 * @return string
	 */
	public function googleMapsUrl() {
		$base_url = 'https://www.google.com/maps/dir/?api=1&map_action=map&destination=';
		$address = join(', ', [
			$this->address_street,
			$this->address_number,
			$this->address_city,
			$this->address_postcode,
			$this->address_province,
			$this->address_region,
			$this->address_country
		]);
		$address_encoded = urlencode($address);
		$final_url = "{$base_url}{$address_encoded}";

		return $final_url;
	}

	/**
	 * User that claimed this venue.
	 * 
	 * @return \App\Models\User
	 */
	public function owner()
	{
		return $this->belongsTo('App\Models\User');
	}

	/**
	 * Categories the venue is in.
	 *
	 * @return \App\Models\Category
	 */
	public function categories()
	{
		return $this->belongsToMany('App\Models\Category');
	}

	/**
	 * Plan this venue is on.
	 * 
	 * @return \App\Models\VenuePlan
	 */
	public function plan()
	{
		return $this->hasOne('App\Models\VenuePlan');
	}

	/**
	 * Query builder to find venues in the specified location bounds.
	 *
	 * @param  Illuminate\Database\Query\Builder  $query   Query builder instance
	 * @param  float                              $ne_lat  North-East latitude (or just North)
	 * @param  float                              $ne_lng  North-East longitude (or just East)
	 * @param  float                              $sw_lat  South-West latitude (or just South)
	 * @param  float                              $sw_lng  South-West longitude (or just West)
     *
	 * @return Illuminate\Database\Query\Builder           Modified query builder
	 */
	public function scopeInBounds($query, $ne_lat, $ne_lng, $sw_lat, $sw_lng)
	{
		return $query->where([
			['geo_latitude', '>=', $sw_lat],
			['geo_latitude', '<=', $ne_lat],
			['geo_longitude', '>=', $sw_lng],
			['geo_longitude', '<=', $ne_lng]
		]);
	}

	/**
	 * Query builder scope to find venues with a distance radius from a given
	 * location.
	 * 
	 * https://gist.github.com/stevenmaguire/3ada3f73f1ad03356cf5
	 *
	 * @param  Illuminate\Database\Query\Builder  $query   Query builder instance
	 * @param  mixed                              $lat     Lattitude of given location
	 * @param  mixed                              $lng     Longitude of given location
	 * @param  integer                            $radius  Optional distance
	 * @param  string                             $units   Optional units
	 *
	 * @return Illuminate\Database\Query\Builder           Modified query builder
	 */
	public function scopeNear($query, $lat, $lng, $radius = 100, $units = "km")
	{
		$units = ($units === "km") ? 6378.10 : 3963.17;
		$lat = (float) $lat;
		$lng = (float) $lng;
		$radius = (double) $radius;
		$lat_column = 'geo_latitude';
		$lng_column = 'geo_longitude';

		return $query->having('distance', '<=' ,$radius)
			->select(DB::raw("*,
				 ($units * ACOS(COS(RADIANS($lat))
						 * COS(RADIANS($lat_column))
						 * COS(RADIANS($lng) - RADIANS($lng_column))
						 + SIN(RADIANS($lat))
						 * SIN(RADIANS($lat_column)))) AS distance")
			)->orderBy('distance','asc');
	}

	/**
	 * Query builder to order venues by distance from a given location.
	 * 
	 * https://gist.github.com/stevenmaguire/3ada3f73f1ad03356cf5
	 * 
	 * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
	 * @param  mixed                              $lat    Lattitude of given location
	 * @param  mixed                              $lng    Longitude of given location
	 * @param  string                             $units  Optional units
	 * 
	 * @return Illuminate\Database\Query\Builder          Modified query builder
	 */
	public function scopeWithDistanceFrom($query, $lat, $lng, $units = 'km')
	{
		$units = ($units === "km") ? 6378.10 : 3963.17;
		$lat = (float) $lat;
		$lng = (float) $lng;
		$lat_column = 'geo_latitude';
		$lng_column = 'geo_longitude';

		// Join with venue_plans to get the distance bonus
		$query->leftJoin('venue_plans', 'venues.id', 'venue_plans.venue_id');

		// Add distance field
		$distance_raw = "$units * ACOS(
							COS(RADIANS($lat)) * COS(RADIANS($lat_column))
							* COS(RADIANS($lng) - RADIANS($lng_column))
							+ SIN(RADIANS($lat)) * SIN(RADIANS($lat_column))
						) AS distance";
		$query->selectRaw($distance_raw);

		// Add distance_with_bonus field by looking at the plans' distance_bonus
		$distance_with_bonus_raw = "(SELECT (distance - (distance / 100 * distance_bonus))) as distance_with_bonus";
		$query->selectRaw($distance_with_bonus_raw);

		// Sort by distance
		$query->orderBy('distance_with_bonus', 'desc');
		$query->orderBy('distance');

		return $query;
	}

	/**
	 * Scope to search for venues with a given name or in a category with that 
	 * same name.
	 * 
	 * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
	 * @param  String                             $name
	 * 
	 * @return Illuminate\Database\Query\Builder          Modified query builder
	 */
	public function scopeWithNameOrCategoryName($query, $name)
	{
		return $query
			->where('venues.name', 'like', "%{$name}%") // Venue name
			->orWhereHas('categories', function($query) use ($name){ // Category name
				$query->where('name', 'like', "%{$name}%");
			});
	}
}
