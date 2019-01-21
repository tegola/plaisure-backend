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

        $added = 0;
        $updated = 0;

        foreach ($data as $row) {
            // Search a previously venue import
            $venueImport = VenueImport::firstOrNew([
                'source_brand' => VenueImport::SOURCE_BRAND_ADMIRAL_UK,
                'source_id' => $row->id
            ]);

            if (!$venueImport->exists) {
                $this->info("Adding {$row->id}: {$row->name}, {$row->address}, {$row->city}");
                $added++;
            } else {
                $this->comment("Updating {$row->id}: {$row->name}, {$row->address}, {$row->city}");
                $updated++;
            }

            // Store downloaded data
            $venueImport->source_data = json_encode($row);
            $venueImport->save();
        }

        // Print summary
        $this->line('');
        $this->line('Done!');
        $this->line("{$added} added, {$updated} updated.");
        $this->line('');
    }
}
