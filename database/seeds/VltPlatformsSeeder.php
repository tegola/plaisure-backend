<?php

use Illuminate\Database\Seeder;
use App\Models\VltPlatform;

class VltPlatformsSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$vlt_platforms = [
			'ace' => 'Ace Interactive',
			'egt' => 'EGT',
			'merkur' => 'Merkur/Ispired',
			'novomatic' => 'Novomatic',
			'spielo' => 'Spielo/IGT',
			'wmg' => 'WMG'
		];

		foreach ($vlt_platforms as $machine_name => $name) {
			VltPlatform::create([
				'machine_name' => $machine_name,
				'name' => $name
			]);
		}
	}
}
