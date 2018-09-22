<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenuePlan extends Model
{
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'config' => 'array'
	];

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * Config as object (normally an array).
	 * 
	 * @param  string $value
	 * @return stdObject
	 */
	public function getConfigAttribute($value) {
		return json_decode($value);
	}

	/**
	 * Venue using this plan.
	 * 
	 * @return \App\Models\Venue
	 */
	public function venue()
	{
	    return $this->belongsTo('App\Models\Venue');
	}
}
