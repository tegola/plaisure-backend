<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
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
	protected $signature = 'venues:from-imports
							{--brand=}
							{--T|skip-timestamps : Whether to disable timestamp checks (import data older than venue data is skipped by default)}';

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
			->with('venues')
			->withTrashed();

		if ($this->hasOption('brand')) {
			switch ($this->option('brand')) {
				case 'aams': $query->where('source_brand', VenueImport::SOURCE_BRAND_AAMS); break;
				case 'admiral-uk': $query->where('source_brand', VenueImport::SOURCE_BRAND_ADMIRAL_UK); break;
				case 'cashino': $query->where('source_brand', VenueImport::SOURCE_BRAND_CASHINO); break;
				case 'megabet': $query->where('source_brand', VenueImport::SOURCE_BRAND_MEGABET); break;
				case 'ladbrokes': $query->where('source_brand', VenueImport::SOURCE_BRAND_LADBROKES); break;
                case 'william-hill-uk': $query->where('source_brand', VenueImport::SOURCE_BRAND_WILLIAM_HILL_UK); break;
                case 'gaming-directory-com': $query->where('source_brand', VenueImport::SOURCE_BRAND_GAMING_DIRECTORY_COM); break;
				default: throw new \Exception('The specified brand is not available.');
			}
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
			// Handle soft deleted imports: skip if still connected to a venue,
			// otherwise force delete them
			// FIXME: Maybe move into the import command and remove handle of
			// trashed imports here?
			if ($venueImport->trashed()) {
				if ($venueImport->venues->count()) {
					$this->warn("Skipped {$venueImport->readableSourceBrand()} {$venueImport->source_id}: is deleted but still connected to one or more venues. Needs admin intervention.");
					$this->skipped++;
				} else {
					$venueImport->forceDelete();
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

			if ($venueImport->venues->count()) {

				// Handle update of connected venues: skip if venue is owned by
				// somebody or venue import data is older than the venue data
				// (by comparing the updated_at field). Otherwise, just plain
				// update it.
				foreach ($venueImport->venues as $venue) {
					if ($venue->owner_id) {
						$this->warn("Skipped {$venueImport->readableSourceBrand()} {$venueImport->source_id}: needs admin intervention to be updated.");
						$this->skipped++;
					} else if ($venueImport->updated_at <= $venue->updated_at && !$this->option('skip-timestamps')) {
						$this->warn("Skipped {$venueImport->readableSourceBrand()} {$venueImport->source_id}: venue data is newer than imported data.");
						$this->skipped++;
					} else {
						DB::transaction(function() use ($venueImport, $venue) {
							try {
								$this->fill($venue, $venueImport);
							} catch (\Exception $e) {
								// Show error and count skipped
								$message = $e->getMessage();
								$this->warn("Skipped {$venueImport->readableSourceBrand()} {$venueImport->source_id}: {$message}.");
								$this->skipped++;

								// Rollback transaction by throwing the
								// original exception
								throw $e;
							}
							// $venue->save();
							$venue->touch(); // Like save, but forces update of timestamps when no attribute has changed

							$this->warn("Updated {$venueImport->readableSourceBrand()} {$venueImport->source_id}: {$venue->address_line1}, {$venue->address_postcode}, {$venue->address_city}.");
							$this->updated++;
						});
					}
					continue; // To next venue related to import
				}

			} else {

				// Handle creation of new venue
				DB::transaction(function() use ($venueImport) {
					$venue = new Venue();
					$venue->save(); // So relations can be attached

					try {
						$this->fill($venue, $venueImport);
					} catch (\Exception $e) {
						// Show error and count skipped
						$message = $e->getMessage();
						$this->warn("Skipped {$venueImport->readableSourceBrand()} {$venueImport->source_id}: {$message}.");
						$this->skipped++;

						// Rollback transaction by throwing the
						// original exception
						throw $e;
					}

					$venueImport->venues()->save($venue);

					$this->info("Added {$venueImport->readableSourceBrand()} {$venueImport->source_id}: {$venue->address_line1}, {$venue->address_postcode}, {$venue->address_city}.");
					$this->added++;
				});

			}
			continue; // To next Import
		}

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

		// Name
		$venue->name = data_get($source, 'name');

		// Address
		$venue->address_line1 = data_get($source, 'address_line1') ?: '';
		$venue->address_line2 = data_get($source, 'address_line2') ?: '';
		$venue->address_city = data_get($source, 'address_city') ?: '';
		$venue->address_postcode = data_get($source, 'address_postcode') ?: '';
		$venue->address_province = data_get($source, 'address_province') ?: '';
		$venue->address_region = data_get($source, 'address_region') ?: '';
		$venue->country = data_get($source, 'country') ?: '';
		$venue->geo_latitude = data_get($source, 'geo_latitude') ?: null;
		$venue->geo_longitude = data_get($source, 'geo_longitude') ?: null;

		// Contacts
		$venue->contact_phone = data_get($source, 'contact_phone') ?: '';
		$venue->contact_email = data_get($source, 'contact_email') ?: '';

		// Urls
		$venue->url_site = data_get($source, 'url_site') ?: '';
		$venue->url_facebook = data_get($source, 'url_facebook') ?: '';

		// Misc data
		$venue->surface_size = data_get($source, 'surface_size', 0);
		$venue->vlt_machine_count = (int) data_get($source, 'vlt_machine_count', 0);
		$venue->parking_capacity = (int) data_get($source, 'parking_capacity', 0);
		$venue->sports_betting = (bool) data_get($source, 'sports_betting');
		$venue->horse_betting = (bool) data_get($source, 'horse_betting');

		// Remove categories
		$venue->categories()->detach();

		foreach ($source['categories'] as $category) {
			$machineName = $category['machine_name'];
			$isPrimary = (bool) data_get($category, 'is_primary', false);

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
