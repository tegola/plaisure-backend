<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class VenueBusinessHour extends Model
{

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = [
		'valid_from',
		'valid_to'
	];

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * The storage format of the model's date columns.
	 *
	 * @var string
	 */
	protected $dateFormat = 'Y-m-d';

	/**
	 * Venue which this business hour is set for.
	 * 
	 * @return \App\Models\Venue
	 */
	public function venue()
	{
		return $this->belongsTo('App\Models\Venue');
	}
	/**
	 * Get the opening time value without seconds.
	 * 
	 * @param  string $value The original value
	 * @return string        The value without the seconds part
	 */
	public function getOpensAttribute($value)
	{
		$found = preg_match('/^\d\d:\d\d/', $value, $matches);

		return $found ? $matches[0] : '';
	}

	/**
	 * Get the closing time value without seconds.
	 * 
	 * @param  string $value The original value
	 * @return string        The value without the seconds part
	 */
	public function getClosesAttribute($value)
	{
		$found = preg_match('/^\d\d:\d\d/', $value, $matches);

		return $found ? $matches[0] : '';
	}

	/**
	 * Get the readable week day.
	 * 
	 * @return string
	 */
	public function readableDay()
	{
		return Carbon::parse('Sunday')->addDay($this->day)->formatLocalized('%A');
	}

	/**
	 * Check whether these hours are compatible with the current time.
	 * 
	 * @return boolean
	 */
	public function isCurrent()
	{
		$now = Carbon::now();
		$day = $now->dayOfWeek;
		$time = $now->format('H:i:s');
		$yesterday = $now->subDay()->dayOfWeek;

		if ($this->opens < $this->closes) {

			// Find a match in today's normal hours
			if ($this->day == $day &&
				$this->opens <= $time &&
				$this->closes >= $time) return true;

		} else if ($this->closes < $this->opens) {

			// Find a match in today's inverted hours, meaning the closing time is
			// in late night, and so is smaller than the opening time
			if ($this->day == $day && $this->opens <= $time) return true;
			if ($this->day == $yesterday && $this->closes >= $time) return true;

		}

		return false;
	}
}
