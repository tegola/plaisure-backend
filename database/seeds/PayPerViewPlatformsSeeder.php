<?php

use Illuminate\Database\Seeder;
use App\Models\PayPerViewPlatform;

class PayPerViewPlatformsSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$pay_per_view_platforms = [
			'mediaset' => 'Mediaset Premium',
			'sky' => 'Sky'
		];

		foreach ($pay_per_view_platforms as $machine_name => $name) {
			PayPerViewPlatform::create([
				'machine_name' => $machine_name,
				'name' => $name
			]);
		}
	}
}
