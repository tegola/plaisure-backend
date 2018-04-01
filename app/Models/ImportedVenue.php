<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportedVenue extends Model
{
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * Query builder to search for text in some fields.
	 * 
	 * @param  Illuminate\Database\Query\Builder $query
	 * @param  string                            $search
	 * 
	 * @return Illuminate\Database\Query\Builder
	 */
	public function scopeSearch($query, string $search)
	{
		if (!$search) return $query;

		return $query
			->where('name', 'like', "%{$search}%")
			->orWhere('address_1', 'like', "%{$search}%")
			->orWhere('address_2', 'like', "%{$search}%")
			->orWhere('aams_census_code', 'like', "%{$search}%");
	}
}
