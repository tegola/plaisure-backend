<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	/**
	 * Venues having this category set
	 *
	 * @return App\Venue
	 */
	public function venues()
	{
		return $this->belongsToMany('App\Venue');
	}
}
