<?php

namespace App\Http\Controllers\Admin\Venue;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVenue;
use App\Models\Venue;
use App\Models\ImportedVenue;
use App\Models\VenuePlan;
use App\Models\Category;
use JavaScript;

class FormController extends Controller
{
	/**
	 * Create a new venue.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$venue = new Venue(old());

		return $this->showForm($venue);
	}

	/**
	 * Edit an existing venue.
	 * 
	 * @param  Venue  $venue
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Venue $venue)
	{
		if (old()) $venue->fill(old());
		$venue->load('plan');

		// If the venus has no geo or address, data, get the original Imported venue
		if ((!$venue->geo_latitude || $venue->geo_latitude || !$venue->address_city || !$venued->address_street) && $venue->aams_census_code) {
			$importedVenue = ImportedVenue::where('aams_census_code', $venue->aams_census_code)->first();
		} else {
			$importedVenue = null;
		}

		return $this->showForm($venue, $importedVenue);
	}

	/**
	 * Actually shows the form view to add/edit a venue.
	 * 
	 * @param  Venue $venue
	 * @return \Illuminate\Http\Response
	 */
	private function showForm(Venue $venue, ImportedVenue $importedVenue = null)
	{
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
			'venue' => $venue
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

	/**
	 * Save over an existing venue.
	 * 
	 * @param  StoreVenue $request
	 * @param  Venue $venue
	 * @return \Illuminate\Http\Response
	 */
	public function update(StoreVenue $request, Venue $venue)
	{
		// Save venue
		$venue->fill($request->all());
		$venue->save();

		// Save categories
		$venue->categories()->sync($request->categories);

		// Save plan
		if ($request->plan) {
			$plan = $venue->plan ?: new VenuePlan;
			$plan->fill($request->plan);
			$venue->plan()->save($plan);
		} else {
			if ($venue->plan) $venue->plan->delete();
		}

		return redirect()->route('admin.venues.index');
	}
}