<?php

namespace App\Normalizers;

use App\Normalizers\Normalizer;

class AdmiralUk extends Normalizer
{
	public function normalize()
	{
		$source = $this->source;

		// Find city and region
		$cityAndRegion = explode(',', $source->city);

		return [
			'name' => $source->name,
			'address_line1' => $source->address,
			'address_city' => trim($cityAndRegion[0]),
			'address_postcode' => $source->postcode,
			'address_region' => count($cityAndRegion) > 1 ? trim($cityAndRegion[1]) : '',
			'geo_latitude' => round($source->lat, 6),
			'geo_longitude' => round($source->lng, 6),
			'contact_phone' => $source->telephone,
			'url_site' => "https://www.admiralslots.co.uk/venue/{$source->link}/"
		];
	}
}