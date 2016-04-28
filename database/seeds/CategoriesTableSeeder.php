<?php

use Illuminate\Database\Seeder;

use App\Category;

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
			'Agenzia scommesse', // 1
			'Ricevitoria',       // 2
			'Sala Bingo',        // 3
			'Sala VLT'           // 4
		];

		foreach ($categories as $name) {
			$category = new Category;

			$category->name = $name;
			$category->save();
		}
	}
}
