<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	/**
	 * Venues having this category set
	 *
	 * @return App\Models\Venue
	 */
	public function venues()
	{
		return $this->belongsToMany('App\Models\Venue');
	}
}
