<?php

namespace App\Http\Controllers\Admin\Venues;

use App\Http\Controllers\Controller;
/*
use App\Http\Requests\StoreVenue;
*/
use App\Models\Venue;
use App\Models\VenueCategory;
use App\Models\Concessionaire;
use App\Models\VltPlatform;
use App\Models\PayPerViewPlatform;
/*
use App\Models\ImportedVenue;
use App\Models\VenuePlan;
use App\Models\VenueBusinessHour;
use App\Models\File;
use JavaScript;
use DB;
*/

class FormController extends Controller
{
	/**
	 * Get the data to show the venue form.
	 * 
	 * @param  Venue  $venue
	 * @return Illuminate\Http\Response
	 */
	public function load(Venue $venue = null)
	{
		// Add venue if none is specified
		if (!$venue) $venue = new Venue();

		// Eager load relationships
		$venue->load([
			'businessHours',
			'categories',
			'payPerViewPlatforms',
			'photos',
			'vltPlatforms',
			'subscriptions',
			'import'
		]);

		// Load satellite data
		$categories = VenueCategory::query()
			->select('id', 'name', 'country')
			->orderBy('country', 'asc')
			->orderBy('name', 'asc')
			->get();
		$concessionaires = Concessionaire::query()
			->select('id', 'name', 'country')
			->orderBy('country', 'asc')
			->orderBy('name', 'asc')
			->get();
		$vltPlatforms = VltPlatform::query()
			->select('id', 'name', 'country')
			->orderBy('country', 'asc')
			->orderBy('name', 'asc')
			->get();
		$payPerViewPlatforms = PayPerViewPlatform::query()
			->select('id', 'name', 'country')
			->orderBy('country', 'asc')
			->orderBy('name', 'asc')
			->get();

		return compact(
			'venue',
			'categories',
			'concessionaires',
			'vltPlatforms',
			'payPerViewPlatforms'
		);
	}

	/**
	 * Create a new venue.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	/*
	public function create()
	{
		$venue = new Venue(old());

		return $this->showForm($venue);
	}
	*/

	/**
	 * Create a new venue from an imported venue.
	 * 
	 * @param  ImportedVenue $importedVenue
	 * @return \Illuminate\Http\Response
	 */
	/*
	public function promote(ImportedVenue $importedVenue)
	{
		// Create the new venue
		$venue = new Venue();

		// Fill new venue with previously posted data or Imported venue data
		if (old()) {
			$venue->fill(old());
		} else {
			$venue->fill([
				'aams_census_code' => $importedVenue->aams_census_code,
				'aams_subject_enrollment_code' => $importedVenue->aams_subject_enrollment_code,
				'name' => $importedVenue->name,
				'surface_size' => $importedVenue->surface_size
			]);
			
			switch ($importedVenue->machine_type) {
				case 'A': $venue->machine_type = Venue::MACHINE_TYPE_A; break;
				case 'B': $venue->machine_type = Venue::MACHINE_TYPE_B; break;
				case 'A/B': $venue->machine_type = Venue::MACHINE_TYPE_AB; break;
			}
		}

		return $this->showForm($venue, $importedVenue);
	}
	*/

	/**
	 * Edit an existing venue.
	 * 
	 * @param  Venue  $venue
	 * @return \Illuminate\Http\Response
	 */
	/*
	public function edit(Venue $venue)
	{
		if (old()) $venue->fill(old());

		$venue->load([
			'businessHours',
			'photos',
			'plan'
		]);

		// If the venus has no geo or address, data, get the original Imported venue
		if ((!$venue->geo_latitude || $venue->geo_latitude || !$venue->address_city || !$venue->address_line1) && $venue->aams_census_code) {
			$importedVenue = ImportedVenue::where('aams_census_code', $venue->aams_census_code)->first();
		} else {
			$importedVenue = null;
		}

		return $this->showForm($venue, $importedVenue);
	}
	*/

	/**
	 * Actually shows the form view to add/edit a venue.
	 * 
	 * @param  Venue $venue
	 * @return \Illuminate\Http\Response
	 */
	/*
	private function showForm(Venue $venue, ImportedVenue $importedVenue = null)
	{
		$venueCategories = $venue->categories()->pluck('id');
		$venueVltPlatforms = $venue->vltPlatforms()->pluck('id');
		$venuePayPerViewPlatforms = $venue->payPerViewPlatforms()->pluck('id');
		
		$machineTypes = Venue::machineTypes();
		$categories = VenueCategory::pluck('name', 'id')->all();
		$concessionaires = Concessionaire::pluck('name', 'id')->all();
		$vltPlatforms = VltPlatform::pluck('name', 'id')->all();
		$payPerViewPlatforms = PayPerViewPlatform::pluck('name', 'id')->all();

		$daysOfWeek = ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'];
		$subscriptions = config('subscriptions');

		JavaScript::put(compact(
			'venue',
			'venueCategories',
			'venueVltPlatforms',
			'venuePayPerViewPlatforms',
			'importedVenue',
			'machineTypes',
			'categories',
			'concessionaires',
			'vltPlatforms',
			'payPerViewPlatforms',
			'subscriptions'
		));

		return view('admin.venues.form', compact('venue', 'daysOfWeek'));
	}
	*/

	/**
	 * Save a new venue.
	 * 
	 * @param  StoreVenue $request
	 * @return \Illuminate\Http\Response
	 */
	/*
	public function store(StoreVenue $request)
	{
		DB::transaction(function() use($request) {
			// Save venue
			$venue = new Venue($request->all());
			$venue->save();

			// Save categories
			$venue->categories()->sync($request->categories);

			// Save VLT platforms
			$venue->vltPlatforms()->sync($request->vlt_platforms);

			// Save pay per view Platforms
			$venue->payPerViewPlatforms()->sync($request->pay_per_view_platforms);

			// Save business hours
			if ($request->business_hours) {
				foreach ($request->business_hours as $hours) {
					$hour = new VenueBusinessHour($hours);
					$venue->businessHours()->save($hour);
				}
			}

			// Save plan
			if ($request->plan) {
				$plan = new VenuePlan();
				$plan->fill($request->plan);
				$venue->plan()->save();
			}
		});

		return redirect()->route('admin.venues.index');
	}
	*/

	/**
	 * Save over an existing venue.
	 * 
	 * @param  StoreVenue $request
	 * @param  Venue $venue
	 * @return \Illuminate\Http\Response
	 */
	/*
	public function update(StoreVenue $request, Venue $venue)
	{
		DB::transaction(function() use ($request, $venue) {
			// Save venue
			$venue->fill($request->all());
			$venue->save();

			// Save categories
			$venue->categories()->sync($request->categories);

			// Save VLT platforms
			$venue->vltPlatforms()->sync($request->vlt_platforms);

			// Save pay per view Platforms
			$venue->payPerViewPlatforms()->sync($request->pay_per_view_platforms);

			// Save business hours
			$venue->businessHours()->delete();
			if ($request->business_hours) {
				foreach ($request->business_hours as $hours) {
					$hour = new VenueBusinessHour($hours);
					$venue->businessHours()->save($hour);
				}
			}

			// Delete discarded photos
			$currentPhotos = $venue->photos();
			if ($request->photos) $currentPhotos = $currentPhotos->whereNotIn('id', $request->photos);

			foreach ($currentPhotos->get() as $file) {
				$file->delete();
			}

			// Store new photos
			if ($request->photos) {
				$uploadedPhotos = File::orphans()->whereIn('id', $request->photos);

				foreach ($uploadedPhotos->get() as $uploadedPhoto) {
					// Set type
					$uploadedPhoto->update([
						'type' => File::TYPE_VENUE_PHOTO
					]);

					// Make the files public
					$uploadedPhoto->makePublic();
				}

				if ($uploadedPhotos->count()) {
					$venue->photos()->saveMany($uploadedPhotos->get());
				}
			}

			// Save plan
			if ($request->plan) {
				$plan = $venue->plan ?: new VenuePlan();
				$plan->fill($request->plan);
				$venue->plan()->save($plan);
			} else {
				if ($venue->plan) $venue->plan->delete();
			}
		});

		return redirect()->route('admin.venues.index');
	}
	*/
}