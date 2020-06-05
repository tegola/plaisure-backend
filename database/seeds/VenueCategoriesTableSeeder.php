<?php

use Illuminate\Database\Seeder;
use App\Models\VenueCategory;

class VenueCategoriesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		foreach ($this->categories as $values) {
			$category = VenueCategory::firstOrNew([
				'machine_name' => $values['machine_name']
			]);

			$category->name = ''; // FIXME: Remove when there's no more name column in db
			$category->country = $values['country'];
			$category->save();
		}
	}

	private $categories = [
		[
			'machine_name' => 'betting_shop',
			'country' => ''
		],
		[
			'machine_name' => 'bingo',
			'country' => ''
		],
		[
			'machine_name' => 'vlt',
			'country' => 'IT'
		],
		[
			'machine_name' => 'adult_gaming_center',
			'country' => 'GB'
		],
		[
			'machine_name' => 'family_entertainment_center',
			'country' => 'GB'
		],
		[
			'machine_name' => 'casino',
			'country' => ''
		],
		[
			'machine_name' => 'card_room',
			'country' => ''
		]
	];
}
