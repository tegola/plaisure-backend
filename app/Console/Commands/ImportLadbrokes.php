<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\VenueImport;

class ImportLadbrokes extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'import:ladbrokes';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import Ladbrokes venues';

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
		$this->line('Importing Ladbrokes venues.');
		$this->line('');

		https://viewer.blipstar.com/searchdbnew?uid=2470030&lat=51.494506&lng=-0.099973&value=50000&max=50000

		// Get the data
		$data = [];
		$client = new Client();
		$centers = [
			[57.4680424, -4.2919821],
			[55.5269664, -3.3482706],
			[55.1928772, -2.7141486],
			[52.337128, -0.074645],
			[51.839323, -2.734778]
		];

		// Run all centers to ensure full coverage
		$this->line('Scanning country in multiple steps.');
		$bar = $this->output->createProgressBar(count($centers));
		$bar->start();

		foreach ($centers as $coords) {
			$response = $client->get('https://viewer.blipstar.com/searchdbnew', [
				'query' => [
					'uid' => 2470030,
					'lat' => $coords[0],
					'lng' => $coords[1],
					'value' => 1000, // Max allowed
					'max' => 1000 // Max allowed
				]
			]);

			// Concatenate to data
			$responseData = json_decode($response->getBody());
			array_shift($responseData); // Remove totals
			$data = array_merge($data, $responseData);

			// Advance progress
			$bar->advance();
		}

		$bar->finish();
		$this->line('');

		// Remove non venues, i.e. those with the total
		$uniqueIds = [];
		$data = array_filter($data, function($row) use (&$uniqueIds) {
			if (!in_array($row->bpid, $uniqueIds)) {
				$uniqueIds[] = $row->bpid;
				return $row;
			}
		});

		// Init counters
		$added = 0;
		$updated = 0;
		$deleted = 0;

		// Add/update open venues
		foreach ($data as $row) {
			// Search a previous import
			$venueImport = VenueImport::firstOrNew([
				'source_brand' => VenueImport::SOURCE_BRAND_LADBROKES,
				'source_id' => $row->bpid
			]);

			if (!$venueImport->exists) {

				// Add
				$venueImport->source_data = $row;
				$venueImport->save();

				$this->info("Added {$row->bpid}: {$row->n}, {$row->ad}");
				$added++;

			} else if ($venueImport->source_data != $row) {

				// Update when data is different
				$venueImport->source_data = $row;
				$venueImport->save();

				$this->comment("Updated {$row->bpid}: {$row->n}, {$row->ad}");
				$updated++;

			}
		}

		// Delete closed venues
		$rowIds = array_map(function($row) {
			return $row->bpid;
		}, $data);
		$closedVenueImports = VenueImport::query()
			->where('source_brand', VenueImport::SOURCE_BRAND_LADBROKES)
			->whereNotIn('source_id', $rowIds)
			->get();

		foreach ($closedVenueImports as $closedVenueImport) {
			$sourceData = $closedVenueImport->source_data;
			$closedVenueImport->delete();

			$this->error("Deleted {$closedVenueImport->source_id}: {$sourceData->n}, {$sourceData->a}");
			$deleted++;
		}

		// Print summary
		$this->line('');
		$this->line('Done!');
		$this->line("{$added} added, {$updated} updated. {$deleted} deleted.");
		$this->line('');
	}
}
