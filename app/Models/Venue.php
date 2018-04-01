<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\File;
use App\Models\VenueBusinessHour;
use DB;
use Auth;
use Carbon;
use Spatie\SchemaOrg\Schema;
use Hashids\Hashids;

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
		'owner_id' => null,
		'concessionaire_id' => null,
		'aams_census_code' => '',
		'aams_subject_enrollment_code' => '',

		'name' => '',
		'description' => '',
		'surface_size' => 0,
		'vlt_machine_count' => 0,
		'awp_machine_count' => 0,
		'seating_capacity' => 0,
		'parking_capacity' => 0,
		'sports_betting' => false,
		'virtual_betting' => false,
		'horse_betting' => false,
		'arcade_roulette' => false,
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
		'url_online_casino' => '',
		'url_facebook' => '',
		'url_tripadvisor' => '',

		'jackpot1_label' => '',
		'jackpot1_value' => 0,
		'jackpot2_label' => '',
		'jackpot2_value' => 0,
		'jackpot3_label' => '',
		'jackpot3_value' => 0,

		'amenity_atm' => false,
		'amenity_bar' => false,
		'amenity_pay_per_view' => false,
		'amenity_pos' => false,
		'amenity_private_parking' => false,
		'amenity_restaurant' => false,
		'amenity_security' => false,
		'amenity_smoking_area' => false,
		'amenity_wifi' => false
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = ['id']; // Only use id_hashed

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'concessionaire_id',
		'aams_census_code',
		'aams_subject_enrollment_code',
		'name',
		'description',
		'surface_size',
		'vlt_machine_count',
		'awp_machine_count',
		'seating_capacity',
		'parking_capacity',
		'sports_betting',
		'virtual_betting',
		'horse_betting',
		'arcade_roulette',
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
		'url_online_casino',
		'url_facebook',
		'url_tripadvisor',
		'jackpot1_label',
		'jackpot1_value',
		'jackpot2_label',
		'jackpot2_value',
		'jackpot3_label',
		'jackpot3_value',
		'amenity_atm',
		'amenity_bar',
		'amenity_pay_per_view',
		'amenity_pos',
		'amenity_private_parking',
		'amenity_restaurant',
		'amenity_security',
		'amenity_smoking_area',
		'amenity_wifi'
	];

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot()
	{
		$user = Auth::user();

		parent::boot();

		// Don't show venues without geo data to normal users
		if (!$user || !$user->is_admin) {
			static::addGlobalScope('noGeoData', function (Builder $builder) {
				$builder->whereNotNull('geo_latitude')
						->whereNotNull('geo_longitude')
						->where('address_street', '!=', '')
						->where('address_city', '!=', '');
			});
		}

		// Automatically create the hashed id
		static::created(function($model) {
			$hasher = new Hashids(static::class, 10);
			$model->id_hashed = $hasher->encode($model->id);
			$model->save();
		});
	}

	/**
	 * Get the route key for the model.
	 *
	 * @return string
	 */
	public function getRouteKeyName()
	{
		return 'id_hashed';
	}

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
	 * Determine if this venue is being managed by an owner.
	 * 
	 * @return boolean
	 */
	public function isManaged()
	{
	    return $this->owner_id ? true : false;
	}

	/**
	 * Build an address array, useful for dividing it in multiple lines.
	 * 
	 * @return array
	 */
	public function addressComponents()
	{
		return [
			$this->address_street . ' ' . $this->address_number,
			$this->address_city,
			$this->address_postcode . ' ' . $this->address_province
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
		if (!$this->distance) return;
		if ($this->distance > 10) return round($this->distance) . ' km';
		if ($this->distance > 1) return round($this->distance, 1) . ' km';
		if ($this->distance < 1) return round($this->distance * 100) . ' m';
	}

	/**
	 * Get the machine name for the first venue category.
	 * 
	 * @return string
	 */
	public function getFirstCategoryMachineNameAttribute()
	{
		$categories = $this->categories();

		return $categories->count() ? $categories->first()->machine_name : '';
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
	 * Get the readable (domain only) site URL.
	 * 
	 * @return string|null
	 */
	public function readableSiteUrl() {
		if (!$this->url_site) return null;

		$parsed = parse_url($this->url_site);
		$domain = str_replace('www.', '', $parsed['host']);

		return $domain ?: null;
	}

	/**
	 * Get the generated Facebook Messenger URL.
	 * 
	 * @return string|null
	 */
	public function facebookMessengerUrl() {
		if (!$this->contact_facebook) return null;

		return implode('', ['https://www.messenger.com/t/', $this->contact_facebook]);
	}

	/**
	 * Get the generated Twitter URL.
	 * 
	 * @return string|null
	 */
	public function twitterUrl() {
		if (!$this->contact_twitter) return null;

		return implode('', ['https://www.twitter.com/', $this->contact_twitter]);
	}

	/**
	 * Checks if this venue has is in the specified category.
	 * 
	 * @param  string  $machine_name
	 * @return boolean
	 */
	public function isInCategory($machine_name)
	{
		return $this->categories()->where('machine_name', $machine_name)->count() ? true : false;
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
	 * Concessionaire this venue is affiliate to.
	 * 
	 * @return \App\Models\Concessionaire
	 */
	public function concessionaire()
	{
		return $this->belongsTo('App\Models\Concessionaire');
	}

	/**
	 * Categories the venue is in.
	 *
	 * @return [\App\Models\VenueCategory]
	 */
	public function categories()
	{
		return $this->belongsToMany('App\Models\VenueCategory');
	}

	/**
	 * VLT platoform this venue belongs to.
	 * 
	 * @return [\App\Models\VltPlatform]
	 */
	public function vltPlatforms()
	{
		return $this->belongsToMany('App\Models\VltPlatform');
	}

	/**
	 * Pay per view platforms available in this venue.
	 * 
	 * @return [\App\Models\PayPerViewPlatform]
	 */
	public function payPerViewPlatforms()
	{
		return $this->belongsToMany('App\Models\PayPerViewPlatform');
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
	 * Photos for this venue.
	 */
	public function photos()
	{
		return $this->morphMany('App\Models\File', 'filable')
				->where('type', File::TYPE_VENUE_PHOTO);
	}

	/**
	 * Business hours for this venue.
	 * 
	 * @return [\App\Models\VenueBusinessHour]
	 */
	public function businessHours()
	{
		return $this
			->hasMany('App\Models\VenueBusinessHour')
			->whereNotNull('day'); // Exclude exception days for now
	}

	/**
	 * Business hours for this venue, grouped by day and exceptions.
	 * 
	 * @param  boolean $includeClosedDays Whether to include days when the venue is closed
	 * @return array
	 */
	public function businessHoursByDay($includeClosedDays = false)
	{
		$days = [
			1 => [],
			2 => [],
			3 => [],
			4 => [],
			5 => [],
			6 => [],
			0 => []
		];

		// Copy business hours in every day
		foreach($this->businessHours as $hours) {
			array_push($days[$hours->day], $hours);
		}

		// Fill empty days with an empty record, or remove the day
		foreach($days as $dayIndex => $hours) {
			if (count($hours)) continue;

			if ($includeClosedDays) {
				$closedHours = new VenueBusinessHour([
					'day' => $dayIndex
				]);
				$days[$dayIndex] = [$closedHours];
			} else {
				unset($days[$dayIndex]);
			}
		}

		return $days;
	}

	/**
	 * Finds out if the venue is open right now.
	 * 
	 * @return boolean
	 */
	public function isOpen()
	{
		$now = Carbon::now();
		$day = $now->dayOfWeek;
		$time = $now->format('H:i:s');

		// Find a match in today's normal hours
		$query = $this->businessHours();
		$query
			->where('day', $day)
			->where('opens', '<=', $time)
			->where('closes', '>=', $time);

		if ($query->count()) return true;

		// Find a match in today's inverted hours, meaning the closing time is
		// in late night, and so is smaller than the opening time
		$query = $this->businessHours();
		$query
			->where('day', $day)
			->whereRaw('closes < opens')
			->where('opens', '<=', $time);

		if ($query->count()) return true;

		// Find a match in yesterday's hours by getting the previous week day
		$day = $now->subDay()->dayOfWeek;

		$query = $this->businessHours();
		$query
			->where('day', $day)
			->whereRaw('closes < opens')
			->where('closes', '>=', $time);

		if ($query->count()) return true;

		// No match
		return false;
	}

	/**
	 * Prepare structured data schema.
	 * 
	 * @return Spatie\SchemaOrg\Schema
	 */
	public function structuredData()
	{
		// Data that doesn't need to be checked
		$schema = Schema::entertainmentBusiness()
			->name($this->name)
			->url(route('site.venues.detail', $this))
			->address($this->long_address) // FIXME: Separare i campi?
			->setProperty('geo', Schema::geoCoordinates()
				->latitude($this->geo_latitude)
				->longitude($this->geo_longitude)
			);

		// Data that need to be checked for existence
		if ($this->description)   $schema->description($this->description);
		if ($this->contact_phone) $schema->telephone($this->contact_phone);
		if ($this->contact_email) $schema->email($this->contact_email);

		// Image
		$photo = $this->photos()->latest()->take(1)->first();

		$schema->image($photo ? $photo->thumbnail_url : [
			asset('img/schema/16x9.png'),
			asset('img/schema/14x3.png'),
			asset('img/schema/1x1.png')
		]);

		// Opening hours
		$hoursSchema = [];

		foreach ($this->businessHours as $hours) {
			$day = substr(date('D', strtotime("Sunday +{$hours->day} days")), 0, 2);

			array_push(
				$hoursSchema,
				Schema::openingHoursSpecification()
					->dayOfWeek($day)
					->opens($hours->opens)
					->closes($hours->closes)
			);
		}
		if (count($hoursSchema)) $schema->setProperty('openingHoursSpecification', $hoursSchema);

		return $schema;
	}

	/**
	 * Venues in the specified location bounds.
	 *
	 * @param  Illuminate\Database\Query\Builder  $query   Query builder instance
	 * @param  float                              $ne_lat  North-East latitude (or just North)
	 * @param  float                              $ne_lng  North-East longitude (or just East)
	 * @param  float                              $sw_lat  South-West latitude (or just South)
	 * @param  float                              $sw_lng  South-West longitude (or just West)
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
	 * Venues with a distance radius from a given location.
	 * 
	 * https://gist.github.com/stevenmaguire/3ada3f73f1ad03356cf5
	 *
	 * @param  Illuminate\Database\Query\Builder  $query   Query builder instance
	 * @param  mixed                              $lat     Lattitude of given location
	 * @param  mixed                              $lng     Longitude of given location
	 * @param  integer                            $radius  Optional distance
	 * @param  string                             $units   Optional units
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

		return $query
			->addSelect(DB::raw("($units * ACOS(COS(RADIANS($lat))
							  * COS(RADIANS($lat_column))
							  * COS(RADIANS($lng) - RADIANS($lng_column))
							  + SIN(RADIANS($lat))
							  * SIN(RADIANS($lat_column)))) AS distance")
			)
			->having('distance', '<=' ,$radius)
			->orderBy('distance','asc');
	}

	/**
	 * Order venues by distance from a given location.
	 * 
	 * https://gist.github.com/stevenmaguire/3ada3f73f1ad03356cf5
	 * 
	 * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
	 * @param  mixed                              $lat    Latitude of given location
	 * @param  mixed                              $lng    Longitude of given location
	 * @param  string                             $units  Optional units
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
	 * Venues that are open right now.
	 * 
	 * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
	 * @return Illuminate\Database\Query\Builder          Modified query builder
	 */
	public function scopeOpen($query)
	{
		return $query->whereHas('businessHours', function($builder) {
			$now = Carbon::now();
			$day = $now->dayOfWeek;
			$time = $now->format('H:i:s');
			$yesterday = $now->subDay()->dayOfWeek;

			$builder
				// Find a match in today's normal hours
				->where([
					['day', $day],
					['opens', '<=', $time],
					['closes', '>=', $time]
				])
				// Find a match in today's inverted hours, meaning the closing
				// time is in late night, and so is smaller than the opening
				// time
				->orWhereRaw('closes < opens')
				->where([
					['day', $day],
					['opens', '<=', $time]
				])
				// Find a match in yesterday's hours by getting the previous
				// week day
				->orWhereRaw('closes < opens')
				->orWhere([
					['day', $yesterday],
					['closes', '>=', $time]
				]);
		});
	}
}
