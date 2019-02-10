<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VenueImport;
use App\Models\Venue;
use App\Models\VenueBusinessHour;
use App\Models\VenueCategory;
use DB;

class VenuesFromImports extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'venues-from-imports {--brand=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create venues from venue import data.';

	/**
	 * Newly added venues count.
	 *
	 * @var integer
	 */
	protected $added = 0;

	/**
	 * Update venues count.
	 *
	 * @var integer
	 */
	protected $updated = 0;

	/**
	 * Skipped imports count.
	 *
	 * @var integer
	 */
	protected $skipped = 0;

	/**
	 * Confirmed venue import deletion count.
	 *
	 * @var integer
	 */
	protected $deleted = 0;

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		// Get venue imports and related venues, optionally limited to specified brand
		$query = VenueImport::query()
			->with('venue')
			->withTrashed();

		switch ($this->option('brand')) {
			case 'admiral-uk': $query->where('source_brand', VenueImport::SOURCE_BRAND_ADMIRAL_UK); break;
			case 'cashino': $query->where('source_brand', VenueImport::SOURCE_BRAND_CASHINO); break;
			case 'megabet': $query->where('source_brand', VenueImport::SOURCE_BRAND_MEGABET); break;
			case 'ladbrokes': $query->where('source_brand', VenueImport::SOURCE_BRAND_LADBROKES); break;
		}

		// Stop if there are no venue imports
		if (!$query->count()) {
			$this->warning('No imports for the specified brand.');
			return;
		}

		// Print intro
		$this->line('');
		$this->line('Creating venues from venue imports...');
		$this->line('');

		foreach ($query->get() as $venueImport) {
			// Handle deleted imports: force delete them if not connected to
			// any venue, otherwise skip them
			if ($venueImport->trashed()) {
				if ($venueImport->venue) {
					$this->warn("Skipped {$venueImport->readableSourceBrand()} {$venueImport->source_id}: is deleted but still connected to a venue.");
					$this->skipped++;
				} else {
					// $venueImport->forceDelete();
					$this->error("Deleted {$venueImport->readableSourceBrand()} {$venueImport->source_id}: confirmed deletion.");
					$this->deleted++;
				}
				continue;
			}

			// Skip imports that don't have enough data
			if (!$venueImport->isReadyForVenue()) {
				$this->warn("Skipped {$venueImport->readableSourceBrand()} {$venueImport->source_id}: not enough data.");
				$this->skipped++;
				continue;
			}

			// Handle update: skip if venue is owned by somebody or venue
			// import data is older than the venue itself. Otherwise, just
			// plain update it
			if ($venueImport->venue) {
				if ($venueImport->venue->owner_id) {
					$this->warn("Skipped {$venueImport->readableSourceBrand()} {$venueImport->source_id}: needs to be updated from an admin.");
					$this->skipped++;
				} else if ($venueImport->updated_at <= $venueImport->venue->updated_at) {
					$this->warn("Skipped {$venueImport->readableSourceBrand()} {$venueImport->source_id}: venue data is newer than imported data.");
					$this->skipped++;
				} else {
					DB::transaction(function() use ($venueImport) {
						$venue = $venueImport->venue;

						try {
							$this->fill($venue, $venueImport);
						} catch (\Exception $e) {
							$message = $e->getMessage();
							$this->warn("Skipped {$venueImport->readableSourceBrand()} {$venueImport->source_id}: $message.");
							$this->skipped++;
							return;
						}

						$venue->save();

						$this->warn("Updated {$venueImport->readableSourceBrand()} {$venueImport->source_id}: {$venue->address_line1}, {$venue->address_postcode}, {$venue->address_city}.");
						$this->updated++;
					});
				}
				continue;
			}

			// Handle creation
			if (!$venueImport->venue) {
				DB::transaction(function() use ($venueImport) {
					$venue = new Venue();
					$venue->save(); // So relations can be attached

					try {
						$this->fill($venue, $venueImport);
					} catch (\Exception $e) {
						$message = $e->getMessage();
						$this->warn("Skipped {$venueImport->readableSourceBrand()} {$venueImport->source_id}: $message().");
						$this->skipped++;
						return;
					}

					$venue->save();

					$venueImport->venue()->associate($venue);
					$venueImport->save();

					$this->info("Added {$venueImport->readableSourceBrand()} {$venueImport->source_id}: {$venue->address_line1}, {$venue->address_postcode}, {$venue->address_city}.");
					$this->added++;
				});

				continue;
			}
		}

		// L'eliminazione di un import collegata a un'attività deve essere tracciata (soft delete e lista in admin tra gli aggiornati)
		// Qui l'aggiunta, aggiornamento e eliminazione devono comportarsi diversamente se la sala è "owned" o no

		// Print import summary
		$this->line('');
		$this->line('Venue creation from imports has finished!');
		$this->line("{$this->added} venues added, {$this->updated} venues updated, {$this->skipped} imports skipped, {$this->deleted} imports deleted.");
		$this->line('');
	}

	/**
	 * Fill Venue with Venue import's normalized data.
	 *
	 * @param  Venue       $venue
	 * @param  VenueImport $venueImport
	 * @return Venue
	 */
	public function fill($venue, $venueImport)
	{
		// Get source as array (recursive)
		// Just casting wouldn't cast sub arrays
		$source = json_decode(json_encode($venueImport->normalized_data), true);

		// Store name
		$venue->name = $source['name'];

		// Store address
		$venue->fill(array_only($source, [
			'address_line1',
			'address_line2',
			'address_city',
			'address_postcode',
			'address_province',
			'address_region',
			'country',
			'geo_latitude',
			'geo_longitude'
		]));

		// Store contacts
		$venue->fill(array_only($source, [
			'contact_phone',
			'url_site'
		]));

		// Remove categories
		$venue->categories()->detach();

		foreach ($source['categories'] as $category) {
			$machineName = $category['machine_name'];
			$isPrimary = array_key_exists('is_primary', $category) ? (bool) $category['is_primary'] : false;

			// Find the category by its machine name
			$venueCategory = VenueCategory::where('machine_name', $machineName)->first();

			// Throw exception if category does not exist
			if (!$venueCategory) {
				throw new \Exception("The specified category ({$machineName}) was not found");
			}

			// Attach category to venue
			$venue->categories()->attach($venueCategory, ['is_primary' => $isPrimary]);
		}

		// Delete business hours
		$venue->businessHours()->delete();


		// Add new business hours (if needed)
		if (array_key_exists('business_hours', $source)) {
			$venue->businessHours()->createMany($source['business_hours']);
		}

		return $venue;
	}
}
