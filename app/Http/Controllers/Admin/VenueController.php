<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use File;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Venue;
use App\Category;
use DB;

class VenueController extends Controller
{

	function __construct()
	{
		// Find current CSV
		$path = storage_path(config('constants.venues_csv_path'));
		$this->csv = File::exists($path) && File::isFile($path) ? $path : null;
	}

	// TODO: In futuro questo file sarà scaricato automaticamente e testato per essere sicuri che non sia diverso
	public function upload(Request $request)
	{
		if ($request->isMethod('post') && $request->hasFile('venues')) {
			$file_new = $request->file('venues');
			$destination = storage_path('app');

			// Check that the file is valid and that it's a CSV (by checking mime type and extension)
			if (!$file_new->isValid()) {
				throw new Exception("Errore nel caricamento del file.");
			}
			if ($file_new->getMimeType() !== 'text/plain' || $file_new->getClientOriginalExtension() !== 'csv') {
				throw new Exception("Errore nel caricamento del file: non è un file CSV.");
			}

			// Move file
			$file_new->move($destination, 'esercizi.csv');

			// TODO: Cancella gli ID del file CSV dalla sessione
			// ...
		}

		return view('admin.venues.upload', [
			'file_current' => $this->csv
		]);
	}

	public function maintain(Request $request)
	{
		$errors = [];
		$venue = false;

		// STEP 1: Find missing venues ----------------------------------------
		// All venues in the CSV files that are missing in the DB

		/*
			- Prendo tutti i codici dal database
		 */

		if ($this->csv) {
			// Get all venues census codes from the DB
			$census_codes = DB::table('venues')->pluck('aams_census_code');

			// Scroll through the csv until an unknown census code is found
			$csv_file = fopen($this->csv, 'r');
			$line_counter = 0;

			while (($line = fgetcsv($csv_file, 0, ';')) !== false) {
				$line_counter++;

				// Skip first line
				if ($line_counter == 1) {
					continue;
				}

				// Check that the census code in the current line exists
				// If it does, skip it, otherwise prepare the data for the form
				$code = trim($line[0]);
				if (!in_array($code, $census_codes)) {
					$venue                               = new Venue;
					$venue->aams_census_code             = $code;          // 0 = CODICE CENSIMENTO ESERCIZIO
					$venue->name                         = trim($line[1]); // 1 = DENOMINAZIONE
					$venue->address_street               = trim($line[2]); // 2 = INDIRIZZO
					$venue->address_city                 = trim($line[3]); // 3 = COMUNE E PROVINCIA
					// $venue->category                  = trim($line[4]); // 4 = TIPOLOGIA ESERCIZIO
					$venue->surface_size                 = trim($line[5]); // 5 = SUPERFICIE DEL LOCALE IN MQ
					$venue->aams_subject_enrollment_code = trim($line[6]); // 6 = CODICE ISCRIZIONE SOGGETTO
					$venue->machine_type                 = trim($line[7]); // 7 = TIPOLOGIA APPARECCHIO
				}
			}
			
		} else {
			// CSV file missing, leave a notice and continue to Step 2
			array_push($errors, 'File CSV non trovato, continuo la modalità di manutenzione mostrando gli esercizi incompleti.');
		}

		// STEP 2: Find venues with missing data ------------------------------
		// In genere sono quelli senza indirizzi o comunque con almeno un dato mancante

		// STEP 3: Find orphan venues -----------------------------------------
		// That is, all venue not found in the CSV file
		// TODO: ...
		
		// Get values for the various selectboxes
		$categories = Category::all();
		$machine_types = Venue::MACHINE_TYPES;

		return view('admin.venues.maintain', array(
			'venue' => $venue,
			'categories' => $categories,
			'machine_types' => $machine_types
		))->withErrors($errors);
	}

}



/*

// FIXME: Salvare i codici in sessione per evitare di caricare il file ogni volta
$csv_file = fopen($this->csv, 'r');
$line_counter = 0;
$census_codes = [];

while (($line = fgetcsv($csv_file, 0, ';')) !== false) {
	$line_counter++;

	// Skip first line
	if ($line_counter == 1) {
		continue;
	}

	// Store census code
	$code = trim($line[0]);
	if (strlen($code)) {
		array_push($census_codes, $code);
	}
}

// Get all venues not found in the database
$venue = DB::table('venues')
				->whereNotIn('aams_census_code', $census_codes)
			 	->take(1)
			 	->get();

 */