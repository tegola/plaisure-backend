<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'short_name',
		'name'
	];

	/**
	 * Venues with this category set.
	 *
	 * @return [App\Models\Venue]
	 */
	public function venues()
	{
		return $this->belongsToMany('App\Models\Venue');
	}
}
