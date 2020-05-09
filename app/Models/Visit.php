<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * Venue that has been visited.
	 *
	 * @return \App\Models\Venue
	 */
	public function venue()
	{
		return $this->belongsTo('App\Models\Venue');
	}
}
