<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use File;
use Carbon\Carbon;

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
		// FIXME: Manca il caso in cui il csv ha i dati aggiornati

		$errors = [];
		$venue = false;
		$venue_original_address = '';
		$mode = $request->input('mode', 'new');

		// Switch to update mode CSV file missing, leave a notice and continue to Step 2
		if (($mode == 'new' || $mode == 'delete') && !$this->csv) {
			array_push($errors, 'File CSV non trovato, continuo la modalità di manutenzione mostrando gli esercizi incompleti.');
			$mode = 'update';
		}

		switch ($mode) {

			// Missing venues -------------------------------------------------
			// All venues in the CSV files that are missing in the DB
			case 'new':
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
						//$venue->address_street               = trim($line[2]); // 2 = INDIRIZZO
						//$venue->address_city                 = trim($line[3]); // 3 = COMUNE E PROVINCIA
						// $venue->category                  = trim($line[4]); // 4 = TIPOLOGIA ESERCIZIO
						$venue->surface_size                 = trim($line[5]); // 5 = SUPERFICIE DEL LOCALE IN MQ
						$venue->aams_subject_enrollment_code = trim($line[6]); // 6 = CODICE ISCRIZIONE SOGGETTO
						$venue->machine_type                 = trim($line[7]); // 7 = TIPOLOGIA APPARECCHIO

						$venue_original_address = trim($line[2]) . ' ' . trim($line[3]);
					}
				}
				break;

			// Venues with missing data ---------------------------------------
			// Existing venues with the least data or a far update date
			case 'update':
				$venue = Venue::where('name', '')
							  ->orWhere('surface_size', 0)
							  ->orWhere('machine_number', 0)
							  ->orWhere('address_street', '')
							  ->orWhere('address_number', '')
							  ->orWhere('address_city', '')
							  ->orWhere('address_postcode', '')
							  ->orWhere('address_province', '')
							  ->orWhere('address_region', '')
							  ->orWhere('address_country', '')
							  ->orWhere('geo_latitude', null)
							  ->orWhere('geo_longitude', null)
							  ->first();
				if (!$venue) {
					$venue = Venue::doesntHave('categories')->first();
				}
				if (!$venue) {
					$max_date = new Carbon('-30 days');
					$venue = Venue::where('updated_at', '<', $max_date)
								  ->first();
				}
				break;

			// Deletable venues -----------------------------------------------
			// Existing venues that haven't been found in the CSV
			case 'delete':
				break;
		}
		
		// Get values for the various selectboxes
		$categories = Category::all();
		$machine_types = Venue::MACHINE_TYPES;

		return view('admin.venues.maintain', array(
			'mode' => $mode,
			'venue' => $venue,
			'venue_original_address' => $venue_original_address,
			'categories' => $categories,
			'machine_types' => $machine_types
		))->withErrors($errors);
	}

	public function store(Request $request)
	{
		$venue_id = $request->input('id');

		if ($venue_id) {
			$venue = Venue::findOrFail($venue_id);
		} else {
			$venue = new Venue;
		}

		dd($venue);
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