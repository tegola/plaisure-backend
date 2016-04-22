<?php

use Illuminate\Database\Seeder;

use App\Venue;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// $venues = factory(App\Venue::class, 20)->create();

		// Create venues by reading the csv file
		// $path = storage_path(config('constants.venues_csv_path'));
		$path = storage_path('app/esercizi-geolocalizzato.csv');

		// Make sure the file exists
		if (!File::exists($path)) {
			$this->command->error("File not found: {$path}");
			return;
		}

		// Load the CSV file
		$file = fopen($path, 'r');


		// Go through each line
		$line_counter = 0;
		while (($line = fgetcsv($file, 0, ';')) !== false) {
			$line_counter++;

			// Skip first line
			if ($line_counter == 1) {
				continue;
			}

			// Store venue
			$venue = new Venue();

			// [0] "CODICE CENSIMENTO ESERCIZIO"
			// [1] DENOMINAZIONE
			// [2] INDIRIZZO
			// [3] "COMUNE E PROVINCIA"
			// [4] "TIPOLOGIA ESERCIZIO"
			// [5] "SUPERFICIE DEL LOCALE IN MQ"
			// [6] "CODICE ISCRIZIONE SOGGETTO"
			// [7] "TIPOLOGIA APPARECCHIO"
			// [8] INDIRIZZO
			// [9] "NUMERO CIVICO"
			// [10] CAP
			// [11] CITTA
			// [12] PROVINCIA
			// [13] REGIONE
			// [14] NAZIONE
			// [15] LATITUDINE
			// [16] LONGITUDINE

			// AAMS data
			$venue->aams_census_code = $line[0];
			$venue->aams_subject_enrollment_code = $line[6];

			// Name and features
			$venue->name = $line[1];
			$venue->type = $line[7];
			$venue->surface_size = $line[5];
			$venue->machine_type = $line[7];

			// Address
			$venue->address_street = isset($line[8]) ? $line[8] : "";
			$venue->address_number = isset($line[9]) ? $line[9] : "";
			$venue->address_city = isset($line[11]) ? $line[11] : "";
			$venue->address_postcode = isset($line[10]) ? $line[10] : "";
			$venue->address_province = isset($line[12]) ? $line[12] : "";
			$venue->address_region = isset($line[13]) ? $line[13] : "";
			$venue->address_country = isset($line[14]) ? $line[14] : "";

			// Geo position
			$venue->geo_latitude = isset($line[15]) ? $line[15] : null;
			$venue->geo_longitude = isset($line[16]) ? $line[16] : null;

			$venue->save();
		}

		$this->command->info('Venues table seeded!');
	}
}
