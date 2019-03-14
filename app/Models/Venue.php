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
	const MACHINE_TYPE_A  = 1;
	const MACHINE_TYPE_B  = 2;
	const MACHINE_TYPE_AB = 3;

	/**
	 * The model's default attributes.
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

		'address_line1' => '',
		'address_line2' => '',
		'address_city' => '',
		'address_postcode' => '',
		'address_province' => '',
		'address_region' => '',

		'country' => '', // See constructor

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
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'sports_betting' => 'boolean',
		'virtual_betting' => 'boolean',
		'horse_betting' => 'boolean',
		'arcade_roulette' => 'boolean',

		'amenity_atm' => 'boolean',
		'amenity_bar' => 'boolean',
		'amenity_pay_per_view' => 'boolean',
		'amenity_pos' => 'boolean',
		'amenity_private_parking' => 'boolean',
		'amenity_restaurant' => 'boolean',
		'amenity_security' => 'boolean',
		'amenity_smoking_area' => 'boolean',
		'amenity_wifi' => 'boolean'
	];

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * Create a new Venue model instance.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	public function __construct(array $attributes = [])
	{
		$user = auth()->user();

		// Default country
		$this->country = $user && $user->locale ? locale_get_region($user->locale) : 'GB';

		parent::__construct($attributes);
	}

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
						->where('address_line1', '!=', '')
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
	 * Determine if this venue has a owner without exposing the owner id.
	 * 
	 * @return boolean
	 */
	public function getHasOwnerAttribute()
	{
	    return $this->owner_id ? true : false;
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
	 * Determine if the Stripe model has a given subscription.
	 *
	 * @param  string  $subscription
	 * @param  string|null  $plan
	 * @return bool
	 */
	public function subscribed()
	{
		$subscription = $this->subscription();

		return $subscription && $subscription->valid();
	}

	/**
	 * Get the subscription instance for this venue
	 * 
	 * @return \App\Models\Subscription
	 */
	public function subscription()
	{
		return $this->subscriptions->first();
	}

	/**
	 * Get all subscriptions for this venue.
	 *
	 * @return \App\Models\Subscription
	 */
	public function subscriptions()
	{
		return $this
			->hasMany('App\Models\Subscription')
			->orderBy('created_at', 'desc');
	}

	/**
	 * Imported venue data to get data from.
	 *
	 * @return \App\Models\VenueImport|null
	 */
	public function venueImport()
	{
		return $this->belongsTo('App\Models\VenueImport');
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
		$days = [[], [], [], [], [], [], []];

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
		$latColumn = 'geo_latitude';
		$lngColumn = 'geo_longitude';

		return $query
			->addSelect(DB::raw("($units * ACOS(COS(RADIANS($lat))
							  * COS(RADIANS($latColumn))
							  * COS(RADIANS($lng) - RADIANS($lngColumn))
							  + SIN(RADIANS($lat))
							  * SIN(RADIANS($latColumn)))) AS distance")
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
		$latColumn = 'geo_latitude';
		$lngColumn = 'geo_longitude';

		// Join with subscriptions to get the distance bonus
		$query->leftJoin('subscriptions', 'venues.id', 'subscriptions.venue_id');

		// Add distance field
		$distanceRaw = "$units * ACOS(
							COS(RADIANS($lat)) * COS(RADIANS($latColumn))
							* COS(RADIANS($lng) - RADIANS($lngColumn))
							+ SIN(RADIANS($lat)) * SIN(RADIANS($latColumn))
						) AS distance";
		$query->selectRaw($distanceRaw);

		// Add distance_with_bonus field by looking at the subscription'a distance_bonus
		$distanceWithBonusRaw = "(SELECT (distance - (distance / 100 * distance_bonus))) as distance_with_bonus";
		$query->selectRaw($distanceWithBonusRaw);

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

	/**
	 * Venues that don't have an owner.
	 * 
	 * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
	 * @return Illuminate\Database\Query\Builder          Modified query builder
	 */
	public function scopeUnclaimed($query)
	{
		return $query->whereNull('owner_id');
	}
}
