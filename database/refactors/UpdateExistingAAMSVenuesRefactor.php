<?php

use App\Models\Venue;
use App\Models\VenueImport;

class UpdateExistingAAMSVenuesRefactor
{
	/**
	 * Run the refactoring.
	 *
	 * @return void
	 */
	public function run()
	{
		// Get all existing AAMS venues without an owner
		$venues = Venue::query()
			->withoutGlobalScopes()
			->whereNull('owner_id')
			->where('aams_census_code', '!=', '')
			->get();

		foreach ($venues as $venue) {
			// Find related import
			$venueImport = VenueImport::query()
				->where('source_brand', VenueImport::SOURCE_BRAND_AAMS)
				->where('source_id', $venue->aams_census_code)
				->first();

			if ($venueImport) {

				// Import found, connect to venue
				echo "Import found: {$venue->id} - {$venueImport->source_id}.\n";

				$venue->venue_import_id = $venueImport->id;
				$venue->save();

			} else {

				// Import not found, delete venue
				echo "Import not found for {$venue->id}, deleting...\n";

				$venue->delete();

			}
		}
	}
}