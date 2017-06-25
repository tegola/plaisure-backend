<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenuePlan extends Model
{
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
