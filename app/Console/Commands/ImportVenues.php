<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VenueImport;
use App\Importers\AdmiralUk as AdmiralUkImporter;
use App\Importers\Cashino as CashinoImporter;
use App\Importers\Ladbrokes as LadbrokesImporter;
use App\Importers\MegaBet as MegabetImporter;

class ImportVenues extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'import-venues {brand}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import venue data from theire respective websites.';

	/**
	 * The Venue importer to use to get data.
	 * 
	 * @var \App\Import\Importers\Importer
	 */
	protected $importer = null;

	/**
	 * Newly added venue import count.
	 * 
	 * @var integer
	 */
	protected $added = 0;

	/**
	 * Updated venue import count.
	 * 
	 * @var integer
	 */
	protected $updated = 0;

	/**
	 * Deleted venue import count.
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
		// Init importer
		switch ($this->argument('brand')) {
			case 'admiral-uk': $this->importer = new AdmiralUkImporter(); break;
			case 'cashino': $this->importer = new CashinoImporter(); break;
			case 'ladbrokes': $this->importer = new LadbrokesImporter(); break;
			case 'megabet': $this->importer = new MegabetImporter(); break;
		}

		// Stop if there's no importer
		if (!$this->importer) {
			$this->error("Error: the specified brand ({$this->argument('brand')}) is not supported.");
			return;
		}

		// Print intro
		$this->line('');
		$this->line('Importing venues from ' . $this->importer->getBrand() . '...');
		$this->line('');

		// Load importer data
		$this->importer->load();

		// Get property keys
		$idKey = $this->importer->getIdKey();

		// Add/update open venues
		foreach ($this->importer->getData() as $item) {
			// Search a previous import
			$venueImport = VenueImport::firstOrNew([
				'source_brand' => $this->importer->getVenueImportBrand(),
				'source_id' => $item->$idKey
			]);

			$normalizedItem = $this->importer->normalizeItem($item);
			$description = $this->importer->getDescriptionForItem($item);

			if (!$venueImport->exists) {

				// Add
				$venueImport->source_data = $item;
				$venueImport->normalized_data = $normalizedItem;
				$venueImport->save();

				$this->info("Added {$item->$idKey}: {$description}");
				$this->added++;

			} else if ($venueImport->source_data != $item || $venueImport->normalized_data != $normalizedItem) {

				// Update when data is different
				$venueImport->source_data = $item;
				$venueImport->normalized_data = $normalizedItem;
				$venueImport->save();

				$this->comment("Updated {$item->$idKey}: {$description}");
				$this->updated++;

			}
		}

		// Delete closed venues
		$outdatedImports = VenueImport::query()
			->where('source_brand', $this->importer->getVenueImportBrand())
			->whereNotIn('source_id', $this->importer->getIds())
			->get();

		foreach ($outdatedImports as $outdatedImport) {
			$sourceData = $outdatedImport->source_data;
			$description = $this->importer->getDescriptionForItem($sourceData);

			$outdatedImport->delete();

			$this->error("Deleted {$outdatedImport->source_id}: {$description}");
			$this->deleted++;
		}

		// Print summary
		$this->line('');
		$this->line('Import done!');
		$this->line("{$this->added} added, {$this->updated} updated, {$this->deleted} deleted.");
		$this->line('');
	}
}
