<?php

namespace App\Http\Controllers\Admin\Venue;

use Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Venue;

class UploadController extends Controller
{
	/**
	 * Shows the upload form.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function form()
	{
		return view('admin.venues.upload', [
			'currentFile' => config('constants.venues_csv_path.filename')
		]);
	}

	/**
	 * Stores the uploaded CSV file.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function save(Request $request)
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

		// Return to form
		return redirect()->route('admin.venues.csv.edit');
	}
}