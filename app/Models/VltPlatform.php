<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VltPlatform extends Model
{
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * Venues belonging to this platform.
	 *
	 * @return [App\Models\Venue]
	 */
	public function venues()
	{
		return $this->belongsToMany('App\Models\Venue');
	}
}
