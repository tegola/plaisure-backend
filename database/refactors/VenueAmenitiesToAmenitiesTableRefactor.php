<?php

use App\Models\Venue;
use App\Models\Amenity;

class VenueAmenitiesToAmenitiesTableRefactor
{
	/**
	 * Run the refactoring.
	 *
	 * @return void
	 */
	public function run()
	{
		$amenities = Amenity::all();
		$venues = Venue::query()
			->where('amenity_atm', true)
			->orWhere('amenity_bar', true)
			->orWhere('amenity_pay_per_view', true)
			->orWhere('amenity_pos', true)
			->orWhere('amenity_private_parking', true)
			->orWhere('amenity_restaurant', true)
			->orWhere('amenity_security', true)
			->orWhere('amenity_smoking_area', true)
			->orWhere('amenity_wifi', true)
			->get();

		foreach ($venues as $venue) {
			// Find out which amenities are set on the venue
			$machineNames = [];

			if ($venue->amenity_atm) $machineNames[] = 'atm';
			if ($venue->amenity_bar) $machineNames[] = 'bar';
			if ($venue->amenity_pay_per_view) $machineNames[] = 'pay_per_view';
			if ($venue->amenity_pos) $machineNames[] = 'pos';
			if ($venue->amenity_private_parking) $machineNames[] = 'private_parking';
			if ($venue->amenity_restaurant) $machineNames[] = 'restaurant';
			if ($venue->amenity_security) $machineNames[] = 'security';
			if ($venue->amenity_smoking_area) $machineNames[] = 'smoking_area';
			if ($venue->amenity_wifi) $machineNames[] = 'wifi';

			// Get only the amenity records that are needed to attach
			$neededAmenities = $amenities->whereIn('machine_name', $machineNames);

			// Attach amenities to venue
			$venue->amenities()->sync($neededAmenities);
		}
	}
}