<?php

namespace App\Normalizers;

use App\Normalizers\Normalizer;

class Ladbrokes extends Normalizer
{
	public function normalize()
	{
		$source = $this->source;

		// Find address
		$address = explode(',', $source->ad);
		$address_line1 = '';
		$address_city = '';

		foreach ($address as $index => $component) {
			$component = trim($component);

			if ($component != $source->pc && $component == strtoupper($component)) {

				// City
				$address_city = trim($component);
				unset($address[$index]);

			} else if ($component == $source->pc) {

				// Post code
				unset($address[$index]);

			}
		}

		$address_line1 = implode(',', $address);

		// Find business hours
		$daysKeys = [
			1 => 'mon',
			2 => 'tue',
			3 => 'wed',
			4 => 'thu',
			5 => 'fri',
			6 => 'sat',
			0 => 'sun'
		];
		$business_hours = [];

		foreach ($daysKeys as $day => $name) {
			// Skip day if empty
			if (!$source->$name) continue;

			$hours = explode('-', $source->$name);
			$opens = date('H:i', strtotime(trim($hours[0])));
			$closes = date('H:i', strtotime(trim($hours[1])));

			$business_hours[] = compact('day', 'opens', 'closes');
		}

		return [
			'name' => $source->n,
			'address_line1' => $address_line1,
			'address_city' => $address_city,
			'address_postcode' => $source->pc,
			'country' => 'UK',
			'geo_latitude' => round($source->lat, 6),
			'geo_longitude' => round($source->lng, 6),
			'business_hours' => $business_hours
		];
	}
}