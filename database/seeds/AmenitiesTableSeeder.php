<?php

use Illuminate\Database\Seeder;
use App\Models\Amenity;

class AmenitiesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		foreach ($this->amenities as $values) {
			$amenity = Amenity::firstOrNew([
				'machine_name' => $values['machine_name']
			]);

			$amenity->country = $values['country'];
			$amenity->save();
		}
	}

	private $amenities = [
		[
			'machine_name' => 'air_conditioning',
			'country' => ''
		],
		[
			'machine_name' => 'atm',
			'country' => ''
		],
		[
			'machine_name' => 'bar',
			'country' => ''
		],
		[
			'machine_name' => 'pay_per_view',
			'country' => ''
		],
		[
			'machine_name' => 'pos',
			'country' => ''
		],
		[
			'machine_name' => 'private_parking',
			'country' => ''
		],
		[
			'machine_name' => 'private_room',
			'country' => ''
		],
		[
			'machine_name' => 'restaurant',
			'country' => ''
		],
		[
			'machine_name' => 'security',
			'country' => ''
		],
		[
			'machine_name' => 'smoking_area',
			'country' => ''
		],
		[
			'machine_name' => 'wifi',
			'country' => ''
		]
	];
}
