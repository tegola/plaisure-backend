<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VenueImport;
use App\Models\Venue;

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
	 * Deleted venues count.
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
		// Get venue imports, optionally limited to specified brand
		$query = VenueImport::query();

		switch ($this->option('brand')) {
			case 'admiral-uk': $query->where('source_brand', VenueImport::SOURCE_BRAND_ADMIRAL_UK); break;
			case 'cashino': $query->where('source_brand', VenueImport::SOURCE_BRAND_CASHINO); break;
			case 'ladbrokes': $query->where('source_brand', VenueImport::SOURCE_BRAND_MEGABET); break;
			case 'megabet': $query->where('source_brand', VenueImport::SOURCE_BRAND_LADBROKES); break;
		}

		$venueImports = $query->get();

		// Stop if there are no venue imports
		if (!$venueImports->count()) {
			$this->warning('No imports for the specified brand.');
			return;
		}

		// Print intro
		$this->line('');
		$this->line('Creating venues from venue imports...');
		$this->line('');


		// L'eliminazione di un import collegata a un'attività deve essere tracciata (soft delete e lista in admin tra gli aggiornati)
		// Qui l'aggiunta, aggiornamento e eliminazione devono comportarsi diversamente se la sala è "owned" o no

		foreach ($venueImports as $venueImport) {
			// Skip imports that are already connected to a venue
			if ($venueImport->venue_id) continue;

			// Create new venue
			$venue = new Venue($venueImport->normalized_data);

			// Associate the venue just created to the import
			$venueImport->venue()->associate($venue);

			// Save both
			$venue->save();
			$venueImport->save();

			$this->added++;
		}

		// Print import summary
		$this->line('');
		$this->line('Venue creation from imports has finished!');
		$this->line("{$this->added} added, {$this->updated} updated, {$this->deleted} deleted.");
		$this->line('');
	}
}
