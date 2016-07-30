<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use File;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Venue;

class VenueController extends Controller
{

	function __construct()
	{
		// Find current CSV
		$path = storage_path(config('venues_csv_path'));
		$this->csv = File::exists($path) ? $path : null;
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
		// STEP 1: Find missing venues ----------------------------------------

		// CSV file missing, leave a notice and continue to Step 2
		if (!$this->csv) {
			array_push($errors, 'File CSV non trovato, continuo la modalità di manutenzione mostrando gli esercizi incompleti.');
			goto step2;
		}

		// Get all venues census codes
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

		// Find all venues in database
		// $new_venues = Venue::whereIn('aams_census_code', $census_codes);

		// Filter census codes by removing existing venues found in DB

		// STEP 2: Find venues with missing data --------------------------
		// In genere sono quelli senza indirizzi o comunque con almeno un dato mancante

		step2: // goto support


		// STEP 3: Find orphan venues -------------------------------------
		// That is, all venue not found in the CSV file
		// TODO: ...

		return view('admin.venues.maintain')->withErrors($errors);
	}

}
