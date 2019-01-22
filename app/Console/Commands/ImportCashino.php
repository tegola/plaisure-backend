<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\VenueImport;

class ImportCashino extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'import:cashino';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import Cashino venues';

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
		$this->line('Importing Cashino venues.');
		$this->line('');

		// Get the data
		$client = new Client();
		$response = $client->get('https://venues.cashino.com/venues.json');
		$data = json_decode($response->getBody());

		// Init counters
		$added = 0;
		$updated = 0;
		$deleted = 0;

		// Add/update open venues
		foreach ($data as $row) {
			// Search a previous import
			$venueImport = VenueImport::firstOrNew([
				'source_brand' => VenueImport::SOURCE_BRAND_CASHINO,
				'source_id' => $row->{'Venue ID'}
			]);

			if (!$venueImport->exists) {

				// Add
				$venueImport->source_data = $row;
				$venueImport->save();

				$this->info("Added {$row->{'Venue ID'}}: {$row->{'Venue Name'}}, {$row->{'Venue Address'}}, {$row->{'Venue Town'}}");
				$added++;

			} else if ($venueImport->source_data != $row) {

				// Update when data is different
				$venueImport->source_data = $row;
				$venueImport->save();

				$this->comment("Updated {$row->{'Venue ID'}}: {$row->{'Venue Name'}}, {$row->{'Venue Address'}}, {$row->{'Venue Town'}}");
				$updated++;

			}
		}

		// Delete closed venues
		$rowIds = array_map(function($row) {
			return $row->{'Venue ID'};
		}, $data);
		$closedVenueImports = VenueImport::query()
			->where('source_brand', VenueImport::SOURCE_BRAND_CASHINO)
			->whereNotIn('source_id', $rowIds)
			->get();

		foreach ($closedVenueImports as $closedVenueImport) {
			$sourceData = $closedVenueImport->source_data;
			$closedVenueImport->delete();

			$this->error("Deleted {$closedVenueImport->source_id}: {$sourceData->{'Venue Name'}}, {$sourceData->{'Venue Address'}}, {$sourceData->{'Venue Town'}}");
			$deleted++;
		}

		// Print summary
		$this->line('');
		$this->line('Done!');
		$this->line("{$added} added, {$updated} updated. {$deleted} deleted.");
		$this->line('');
	}
}
