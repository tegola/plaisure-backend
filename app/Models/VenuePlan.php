<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenuePlan extends Model
{
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];

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
