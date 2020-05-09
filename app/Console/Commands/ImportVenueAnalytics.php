<?php

namespace App\Console\Commands;

use Analytics;
use App\Models\Venue;
use Carbon;
use Illuminate\Console\Command;
use Spatie\Analytics\Period;

class ImportVenueAnalytics extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'analytics:import {--days=1}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import fresh data from Google Analytics';

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
		$days = (int) $this->option('days');
		$urlRegex = '^(\/[a-z]{2})?\/venues\/([a-zA-Z\d]{10})$';

		// Get all visits to venue pages in the last specified number of days
		$response = Analytics::performQuery(
			Period::days($days),
			'ga:sessions',
			[
				'dimensions' => 'ga:date,ga:pagePath',
				'sort' => 'ga:date',
				'filters' => "ga:pagePath=~{$urlRegex}",
				// 'include-empty-rows' => false // Not working
			]
		);

		// Get the data and group it by venue hash
		$rows = collect($response->rows ?? [])
			->filter(function($row) {
				return (int) $row[2]; // Exclude 0-values, include-empty-rows wasn't working
			})
			->map(function ($row) {
				return [
					'date' => $row[0],
					'url' => $row[1],
					'count' => (int) $row[2]
				];
			});

		$rowsByHash = $rows->groupBy(function($row) use ($urlRegex) {
			preg_match("/{$urlRegex}/", $row['url'], $matches);

			return $matches[2];
		});

		foreach ($rowsByHash as $hash => $rows) {
			$venue = Venue::find(Venue::decodeHashedId($hash));

			// Skip venue if not found
			if (!$venue) continue;

			foreach ($rows as $row) {
				$date = Carbon::parse($row['date']);

				$visit = $venue->visits()->firstOrNew(['date' => $date]);
				$visit->count = $row['count'];
				$visit->save();
			}
		}
	}
}
