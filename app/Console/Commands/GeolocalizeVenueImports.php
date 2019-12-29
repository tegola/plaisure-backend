<?php

namespace App\Console\Commands;

use App\Models\VenueImport;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class GeolocalizeVenueImports extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'venues:geolocalize-imports {--brand=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Update imported venues' normalized data with precise geolocalization.";

	/**
	 * Newly added geocode data count.
	 *
	 * @var integer
	 */
	protected $added = 0;

	/**
	 * Skipped imports count.
	 *
	 * @var integer
	 */
	protected $skipped = 0;

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
		// Get venue imports and related venues, optionally limited to specified
		// brand
		$query = VenueImport::query()
			->with('venues')
			->withTrashed();

		if ($brand = $this->option('brand')) {
			switch ($brand) {
				case 'aams': $query->where('source_brand', VenueImport::SOURCE_BRAND_AAMS); break;
				default: throw new \Exception('Geolocalization is not available for the specified brand.');
			}
		} else {
			$query->whereIn('source_brand', [
				VenueImport::SOURCE_BRAND_AAMS
			]);
		}

		// Stop if there are no venue imports
		if (!$query->count()) {
			$this->warning('No imports for the specified brand.');
			return;
		}

		// Create a client
		$client = new Client();

		// Cycle venue imports
		foreach ($query->get() as $venueImport) {
			// Skip if geocode data is already present
			if (optional($venueImport->normalized_data)->geo_latitude &&
				optional($venueImport->normalized_data)->geo_longitude) {
				$this->warn("Skipped {$venueImport->readableSourceBrand()} {$venueImport->source_id}: geocode data already present.");
				$this->skipped++;
				continue;
			}

			// Get venue data for geocoding purposes
			$address = $venueImport->addressForGeocode();
			$country = data_get($venueImport->normalized_data, 'country')
				?: data_get($venueImport->source_data, 'country');

			// Skip if address is not available
			if (!$address) {
				$this->warn("Skipped {$venueImport->readableSourceBrand()} {$venueImport->source_id}: no address available for geocoding.");
				$this->skipped++;
				continue;
			}

			// Limit to 10 requests per second
			usleep(0.1 * 1000000); // 0.1 seconds, sleep(0.1) won't work

			// Pick the language for the geocoder
			switch ($country) {
				case 'IT': $geocodeLanguage = 'it'; break;
				case 'GB': $geocodeLanguage = 'en-GB'; break;
				default: $geocodeLanguage = 'en';
			}

			// Geocode using Google Maps
			$request = $client->request('get', 'https://maps.googleapis.com/maps/api/geocode/json', [
				'query' => [
					'key' => env('GOOGLE_MAPS_KEY'),
					'language' => $geocodeLanguage,
					'address' => $address
				]
			]);
			$response = json_decode($request->getBody()->getContents());

			// Skip if geocode failed
			if ($response->status != 'OK') {
				$this->warn("Skipped {$venueImport->readableSourceBrand()} {$venueImport->source_id}: geocode error ({$response->status}).");
				$this->skipped++;
				continue;
			}

			// Update normalized data with geocode data
			$firstResult = $response->results[0];
			$normalizedData = $venueImport->normalized_data;
			$streetNumber = null;

			foreach ($firstResult->address_components as $component) {
				// Store street number to be added at the end
				if (in_array('street_number', $component->types)) {
					$streetNumber = $component->long_name;
				}

				if (in_array('route', $component->types)) {
					$normalizedData->address_line1 = $component->long_name;
				}
				if (in_array('postal_code', $component->types)) {
					$normalizedData->address_postcode = $component->long_name;
				}
				if (in_array('locality', $component->types)) {
					$normalizedData->address_city = $component->long_name;
				} else if (in_array('administrative_area_level_3', $component->types)) {
					$normalizedData->address_city = $component->long_name;
				}
				if (in_array('administrative_area_level_2', $component->types)) {
					$normalizedData->address_province = $component->long_name;
				}
				if (in_array('administrative_area_level_1', $component->types)) {
					$normalizedData->address_region = $component->long_name;
				}
				if (in_array('country', $component->types)) {
					$normalizedData->country = $component->short_name;
				}
			}

			// Remove "Provincia di" from italian provinces
			if (data_get($normalizedData, 'country') === 'IT' && data_get($normalizedData, 'address_province')) {
				$normalizedData->address_province = str_replace('Provincia di ', '', $normalizedData->address_province);
			}

			if ($streetNumber) {
				$normalizedData->address_line1 .= ", {$streetNumber}";
			}
			if ($firstResult->geometry->location->lat) {
				$normalizedData->geo_latitude = $firstResult->geometry->location->lat;
			}
			if ($firstResult->geometry->location->lng) {
				$normalizedData->geo_longitude = $firstResult->geometry->location->lng;
			}

			// Aggiunge la geolocalizzazione alla riga
			$venueImport->normalized_data = $normalizedData;
			$venueImport->save();

			$addressLine1 = data_get($normalizedData, 'address_line1');
			$addressCity = data_get($normalizedData, 'address_city');

			$this->info("Added {$venueImport->readableSourceBrand()} {$venueImport->source_id}: {$addressLine1}, {$addressCity}.");
			$this->added++;
		}

		// Print summary
		$this->line('');
		$this->line('Geolocalization completed!');
		$this->line("{$this->added} added, {$this->skipped} skipped.");
		$this->line('');
	}
}