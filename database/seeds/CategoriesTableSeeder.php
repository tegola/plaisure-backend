<?php

use Illuminate\Database\Seeder;

use App\Models\VenueCategory;

class CategoriesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$categories = [
			'betting_agency' => 'Agenzia scommesse', // 1
			'bingo' => 'Sala Bingo',                 // 2
			'vlt' => 'Sala VLT'                      // 3
			// 'betting_office' => 'Ricevitoria',       // 4
		];

		foreach ($categories as $machine_name => $name) {
			VenueCategory::create([
				'machine_name' => $machine_name,
				'name' => $name
			]);
		}
	}
}
