<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Normalizers\AdmiralUk as AdmiralUkNormalizer;
use App\Normalizers\Cashino as CashinoNormalizer;
use App\Normalizers\Megabet as MegabetNormalizer;
use App\Normalizers\Ladbrokes as LadbrokesNormalizer;

class VenueImport extends Model
{
	const SOURCE_BRAND_ADMIRAL_UK = 1;
	const SOURCE_BRAND_CASHINO    = 2;
	const SOURCE_BRAND_MEGABET    = 3;
	const SOURCE_BRAND_LADBROKES  = 4;
	
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
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = ['normalized_data'];

	/**
	 * Get the normalized data attribute.
	 * 
	 * @return array
	 */
	public function getNormalizedDataAttribute()
	{
		$source = $this->source_data;

		switch ($this->source_brand) {
			case self::SOURCE_BRAND_ADMIRAL_UK: return new AdmiralUkNormalizer($source);
			case self::SOURCE_BRAND_CASHINO: return new CashinoNormalizer($source);
			case self::SOURCE_BRAND_MEGABET: return new MegabetNormalizer($source);
			case self::SOURCE_BRAND_LADBROKES: return new LadbrokesNormalizer($source);
		}
	}

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
