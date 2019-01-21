<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueImport extends Model
{
	const SOURCE_BRAND_ADMIRAL_UK = 1;
	
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'source_data' => 'object'
	];

	/**
	 * Venue belonging to this import.
	 *
	 * @return [App\Models\Venue]
	 */
	public function venue()
	{
		return $this->belongsTo('App\Models\Venue');
	}
}
