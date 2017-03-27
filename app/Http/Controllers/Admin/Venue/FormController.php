<?php

namespace App\Http\Controllers\Admin\Venue;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVenue;
use App\Models\Venue;
use App\Models\Category;
use View;
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
		$this->venue = new Venue;

		return $this->showForm();
	}

	/**
	 * Edit an existing venue.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Venue $venue)
	{
		$this->venue = $venue;

		return $this->showForm();
	}

	/**
	 * Actually shows the form view to add/edit a venue.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	private function showForm()
	{
		$machineTypes = Venue::machineTypes();
		$categories = Category::pluck('name', 'id')->all();
		$venueCategories = $this->venue->categories()->pluck('id');

		JavaScript::put([
			'venue' => $this->venue,
			'venueCategories' => $venueCategories,
			'machineTypes' => $machineTypes,
			'categories' => $categories
		]);

		return view('admin.venues.form', [
			'venue' => $this->venue
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
		$venue = new Venue($request);
		$venue->save();

		return back();
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
		$venue->fill($request);
		$venue->save();

		return back();
	}
}