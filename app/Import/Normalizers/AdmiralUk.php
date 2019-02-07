<?php

namespace App\Import\Normalizers;

class AdmiralUk extends Normalizer
{
	public function normalize()
	{
		$source = $this->source;

		// Find city and province
		$cityAndProvince = explode(',', $source->city);

		return [
			'name' => $source->name,
			'address_line1' => $source->address,
			'address_city' => trim($cityAndProvince[0]),
			'address_postcode' => $source->postcode,
			'address_province' => count($cityAndProvince) > 1 ? trim($cityAndProvince[1]) : '',
			'country' => 'UK',
			'geo_latitude' => round($source->lat, 6),
			'geo_longitude' => round($source->lng, 6),
			'contact_phone' => $source->telephone,
			'url_site' => "https://www.admiralslots.co.uk/venue/{$source->link}/"
		];
	}
}