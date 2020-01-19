<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\File;

class DeleteOrphanFiles extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'files:delete-orphans';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete orphan files';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$date = now()->subDay(3);
		$files = File::orphans()->where('created_at', '<', $date);
		$count = $files->count();

		$this->comment("Searching orphan files older than {$date}...");
		$this->info("{$count} files found.");

		// Stop if no files were found
		if (!$count) return;

		$bar = $this->output->createProgressBar($count);

		foreach (File::orphans()->get() as $file) {
			$file->delete();
			$bar->advance();
		}

		$bar->finish();
		$this->line('');
		$this->info('Orphan files deleted!');
	}
}
