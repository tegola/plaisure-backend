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

	/**
	 * Scope to items for the specified country.
	 *
	 * @param  Illuminate\Database\Query\Builder  $query   Query builder instance
	 * @param  String                             $country The country to limit to
	 * @return Illuminate\Database\Query\Builder           Modified query builder
	 */
	public function scopeForCountry($query, String $country)
	{
		return $query->whereIn('country', [$country, '']);
	}
}
