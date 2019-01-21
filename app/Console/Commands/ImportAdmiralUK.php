<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\VenueImport;

class ImportAdmiralUK extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'import:admiral-uk';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import Admiral UK venues';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		// Print intro
		$this->line('Importing Admiral UK venues.');
		$this->line('');

		// Get the data
		$client = new Client();
		$response = $client->get('https://www.admiralslots.co.uk/venues.json');
		$data = json_decode($response->getBody());

		// Init counters
		$added = 0;
		$updated = 0;
		$deleted = 0;

		// Add/update open venues
		foreach ($data as $row) {
			// Search a previous import
			$venueImport = VenueImport::firstOrNew([
				'source_brand' => VenueImport::SOURCE_BRAND_ADMIRAL_UK,
				'source_id' => $row->id
			]);

			if (!$venueImport->exists) {

				// Add
				$venueImport->source_data = $row;
				$venueImport->save();

				$this->info("Added {$row->id}: {$row->name}, {$row->address}, {$row->city}");
				$added++;

			} else if ($venueImport->source_data != $row) {

				// Update when data is different
				$venueImport->source_data = $row;
				$venueImport->save();

				$this->comment("Updated {$row->id}: {$row->name}, {$row->address}, {$row->city}");
				$updated++;

			}
		}

		// Delete closed venues
		$rowIds = array_map(function($row) {
			return $row->id;
		}, $data);
		$closedVenueImports = VenueImport::whereNotIn('source_id', $rowIds)->get();

		foreach ($closedVenueImports as $closedVenueImport) {
			$sourceData = $closedVenueImport->source_data;
			$closedVenueImport->delete();

			$this->error("Deleted {$closedVenueImport->source_id}: {$sourceData->name}, {$sourceData->address}, {$sourceData->city}");
			$deleted++;
		}

		// Print summary
		$this->line('');
		$this->line('Done!');
		$this->line("{$added} added, {$updated} updated. {$deleted} deleted.");
		$this->line('');
	}
}
