<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Concessionaire extends Model
{
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * Venues affiliate to this concessionaire.
	 * 
	 * @return [\App\Models\Venue]
	 */
	public function venues()
	{
		return $this->hasMany('App\Models\Venue');
	}
}
