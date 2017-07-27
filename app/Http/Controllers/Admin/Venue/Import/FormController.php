<?php

namespace App\Http\Controllers\Admin\Venue\Import;

use Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ImportedVenue;

class FormController extends Controller
{
	/**
	 * Shows the upload form.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function edit()
	{
		// Get last import date by reading the latest entry date
		$latest = ImportedVenue::latest()->first();
		$date = $latest ? $latest->created_at : null;

		return view('admin.venues.import.form', [
			'lastImport' => $date
		]);
	}

	/**
	 * Stores the uploaded CSV file.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request)
	{
		// Validate request
		$this->validate($request, [
			'file' => 'required|file|mimetypes:text/plain,text/csv'
		]);

		// Get the file
		$file = $request->file('file');

		// Stop if there was some file error
		abort_if(!$file->isValid(), 500, "C'è stato un errore con il file caricato.");

		// Store the file
		$file->storeAs(config('constants.venues_csv_path.dirname'), config('constants.venues_csv_path.filename'));

		// Clear the Imported venues list
		ImportedVenue::truncate();

		// Load the CSV file
		$csvPath = config('constants.venues_csv_path');
		$csvFile = fopen(storage_path(implode('/', $csvPath)), 'r');
		$lineCounter = 0;

		// Scroll through CSV lines and store all of them as Imported venues
		while (($line = fgetcsv($csvFile, 0, ';')) !== false) {
			$lineCounter++;

			// Skip first line
			if ($lineCounter == 1) {
				continue;
			}

			$aams_census_code             = trim($line[0]); // 0 = CODICE CENSIMENTO ESERCIZIO
			$name                         = trim($line[1]); // 1 = DENOMINAZIONE
			$address_1                    = trim($line[2]); // 2 = INDIRIZZO
			$address_2                    = trim($line[3]); // 3 = COMUNE E PROVINCIA
			$type                         = trim($line[4]); // 4 = TIPOLOGIA ESERCIZIO
			$surface_size                 = trim($line[5]); // 5 = SUPERFICIE DEL LOCALE IN MQ
			$aams_subject_enrollment_code = trim($line[6]); // 6 = CODICE ISCRIZIONE SOGGETTO
			$machine_type                 = trim($line[7]); // 7 = TIPOLOGIA APPARECCHIO

			// Skip if there's no data to find the venue
			if (!$aams_census_code || !$name || (!$address_1 && !$address_2)) continue;

			ImportedVenue::create([
				'aams_census_code'             => $aams_census_code,
				'name'                         => $name,
				'address_1'                    => $address_1,
				'address_2'                    => $address_2,
				'type'                         => $type,
				'surface_size'                 => $surface_size,
				'aams_subject_enrollment_code' => $aams_subject_enrollment_code,
				'machine_type'                 => $machine_type
			]);
		}

		// FIXME: search for duplicates via aams_census_code

		// Return to form
		return redirect()->route('admin.venues.unmanaged.index');
	}
}