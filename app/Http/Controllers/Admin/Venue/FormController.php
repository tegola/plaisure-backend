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
		$venue = new Venue(old());

		return $this->showForm($venue);
	}

	/**
	 * Edit an existing venue.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Venue $venue)
	{
		$venue = old() ? $venue->fill(old()) : $venue;

		return $this->showForm($venue);
	}

	/**
	 * Actually shows the form view to add/edit a venue.
	 * 
	 * @param  \App\Models\Venue $venue
	 * @return \Illuminate\Http\Response
	 */
	private function showForm($venue)
	{
		$machineTypes = Venue::machineTypes();
		$categories = Category::pluck('name', 'id')->all();
		$venueCategories = $venue->categories()->pluck('id');

		JavaScript::put([
			'venue' => $venue,
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

		return redirect()->route('admin.venues.index');
	}
}