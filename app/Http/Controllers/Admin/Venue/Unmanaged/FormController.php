<?php

namespace App\Http\Controllers\Admin\Venue\Unmanaged;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVenue;
use App\Models\Venue;
use App\Models\ImportedVenue;
use App\Models\Category;
use JavaScript;

class FormController extends Controller
{
	/**
	 * Promote an imported venue to a new venue.
	 * 
	 * @param  ImportedVenue $importedVenue
	 * @return \Illuminate\Http\Response
	 */
	public function promote(ImportedVenue $importedVenue)
	{
		// Create the new venue
		$venue = new Venue();

		// Fill new venue with previously posted data or Imported venue data
		if (old()) {
			$venue->fill(old());
		} else {
			$venue->aams_census_code = $importedVenue->aams_census_code;
			$venue->aams_subject_enrollment_code = $importedVenue->aams_subject_enrollment_code;
			$venue->name = $importedVenue->name;
			$venue->surface_size = $importedVenue->surface_size;

			switch ($importedVenue->machine_type) {
				case 'A': $venue->machine_type = Venue::MACHINE_TYPE_A; break;
				case 'B': $venue->machine_type = Venue::MACHINE_TYPE_B; break;
				case 'A/B': $venue->machine_type = Venue::MACHINE_TYPE_AB; break;
			}
		}

		$machineTypes = Venue::machineTypes();
		$categories = Category::pluck('name', 'id')->all();
		$venueCategories = $venue->categories()->pluck('id');

		JavaScript::put([
			'venue' => $venue,
			'importedVenue' => $importedVenue,
			'venueCategories' => $venueCategories,
			'machineTypes' => $machineTypes,
			'categories' => $categories
		]);

		return view('admin.venues.form', [
			'venue' => $venue,
			'importedVenue' => $importedVenue
		]);
	}

	/**
	 * Save a new venue.
	 * 
	 * @param  StoreVenue $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(StoreVenue $request)
	{
		// Save venue
		$venue = new Venue($request);
		$venue->save();

		// Save categories
		$venue->categories()->sync($request->categories);

		return redirect()->route('admin.venues.index');
	}
}