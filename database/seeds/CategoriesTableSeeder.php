<?php

use Illuminate\Database\Seeder;

use App\Models\Category;

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

		foreach ($categories as $short_name => $name) {
			Category::create([
				'short_name' => $short_name,
				'name' => $name
			]);
		}
	}
}
