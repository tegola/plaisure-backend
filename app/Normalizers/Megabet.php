<?php

namespace App\Normalizers;

use App\Normalizers\Normalizer;

class Megabet extends Normalizer
{
	public function normalize()
	{
		$source = $this->source;

		return [
			'name' => "Megabet",
			'address_postcode' => $source->postcode,
			'country' => 'UK',
			'geo_latitude' => round($source->latitude, 6),
			'geo_longitude' => round($source->longitude, 6)
		];
	}
}