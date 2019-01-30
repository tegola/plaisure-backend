<?php

namespace App\Normalizers;

use App\Normalizers\Normalizer;

class Cashino extends Normalizer
{
	public function normalize()
	{
		$source = $this->source;

		// Cleanup address (remove town)
		$address = str_replace($source->{'Venue Town'}, '', $source->{'Venue Address'});
		$address = trim($address);
		$address = preg_replace('/,$/', '', $address);

		// Find coords
		$coords = explode(',', $source->{'Coordinates'});

		// Find categories
		$categories = [];
		if ($source->{'AGC Venue'}) $categories[] = 'Adult Gaming Centre';
		if ($source->{'Bingo Venue'}) $categories[] = 'Bingo';
		if ($source->{'Bingo Express'}) $categories[] = 'Bingo Express';
		if ($source->{'Bingo Plus'}) $categories[] = 'Bingo Plus';
		if ($source->{'Cash Bingo'}) $categories[] = 'Cash Bingo';
		if ($source->{'FEC Venue'}) $categories[] = 'Family Entertainment Centre';

		// FIXME: Trovare gli orari

		return [
			'name' => $source->{'Venue Name'},
			'address_line1' => $address,
			'address_city' => $source->{'Venue Town'},
			'address_postcode' => $source->{'Post Code'},
			'country' => 'UK',
			'geo_latitude' => (float) $coords[0],
			'geo_longitude' => (float) $coords[1],
			'contact_phone' => $source->{'Tel Number'},
			'categories' => $categories
		];
	}
}