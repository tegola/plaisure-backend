<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;
use App\Models\VenueImport;

class ImportMegabet extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'import:megabet';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import Megabet venues';

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
		$this->line('Importing Megabet venues.');
		$this->line('');

		// Get the web page
		$client = new Client();
		$crawler = $client->request('GET', 'http://www.megabet.co.uk/p/shop-locator/');

		// Build the data
		$data = [];

		// Loop through venue rows
		$crawler->filter('#RightContainer .table tbody tr')->each(function($tr) use (&$data) {
			$row = new \stdClass();

			// Loop through cells of each row
			$tr->filter('td')->each(function($td) use (&$row) {
				$tdClass = $td->attr('class');

				if ($tdClass == 'shopName') {
					$row->name = $td->text();
				} else if ($tdClass == 'shopAddress') {
					$row->address = $td->text();
				} else if ($tdClass == 'shopPostcode') {
					$row->postcode = $td->text();
				} else if ($tdClass == 'shopMap') {
					$td->filter('.shopLink')->each(function($link) use (&$row) {
						$href = $link->attr('href');
						$geoDataString = explode('Current+Location/', $href)[1];
						$geoDataArray = explode(',', $geoDataString);
						$row->latitude = (float) $geoDataArray[0];
						$row->longitude = (float) $geoDataArray[1];
					});
				}
			});

			// Generate an id for the row
			$row->generated_id = substr(md5($row->name . $row->postcode), 0, 8);

			array_push($data, $row);
		});

		// Init counters
		$added = 0;
		$updated = 0;
		$deleted = 0;

		// Add/update open venues
		foreach ($data as $row) {
			// Search a previous import
			$venueImport = VenueImport::firstOrNew([
				'source_brand' => VenueImport::SOURCE_BRAND_MEGABET,
				'source_id' => $row->generated_id
			]);

			if (!$venueImport->exists) {

				// Add
				$venueImport->source_data = $row;
				$venueImport->save();

				$this->info("Added {$row->generated_id}: {$row->name}, {$row->address}, {$row->postcode}");
				$added++;

			} else if ($venueImport->source_data != $row) {

				// Update when data is different
				$venueImport->source_data = $row;
				$venueImport->save();

				$this->comment("Updated {$row->generated_id}: {$row->name}, {$row->address}, {$row->postcode}");
				$updated++;

			}
		}

		// Delete closed venues
		$rowIds = array_map(function($row) {
			return $row->generated_id;
		}, $data);
		$closedVenueImports = VenueImport::query()
			->where('source_brand', VenueImport::SOURCE_BRAND_MEGABET)
			->whereNotIn('source_id', $rowIds)
			->get();

		foreach ($closedVenueImports as $closedVenueImport) {
			$sourceData = $closedVenueImport->source_data;
			$closedVenueImport->delete();

			$this->error("Deleted {$closedVenueImport->source_id}: {$sourceData->name}, {$sourceData->address}, {$sourceData->postcode}");
			$deleted++;
		}

		// Print summary
		$this->line('');
		$this->line('Done!');
		$this->line("{$added} added, {$updated} updated. {$deleted} deleted.");
		$this->line('');
	}
}
