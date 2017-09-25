<?php

use Illuminate\Database\Seeder;
use App\Models\Concessionaire;

class ConcessionairesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$concessionaires = [
			'admiral' => 'Admiral Gaming Network',
			'cirsa' => 'Cirsa',
			'codere' => 'Codere',
			'gamenet' => 'Gamenet',
			'starnet' => 'Global Starnet',
			'hbg' => 'HBG',
			'lottomatica' => 'Lottomatica',
			'netwin' => 'Netwin Italia',
			'nts' => 'NTS Network',
			'sisal' => 'Sisal',
			'snai' => 'SNAI'
		];

		foreach ($concessionaires as $machine_name => $name) {
			Concessionaire::create([
				'machine_name' => $machine_name,
				'name' => $name
			]);
		}
	}
}
