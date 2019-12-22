<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PostDeploySetup extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'deploy:post-setup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Setup the project right after a deploy.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		/*
		/opt/plesk/php/7.2/bin/php /usr/lib64/plesk-9.0/composer.phar install
		rm storage
		ln -sT /var/www/vhosts/plaisure.com/storage/ storage
		/opt/plesk/php/7.2/bin/php artisan migrate --force
		*/

		if (app()->isLocal()) {
			$php = 'php';
			$composer = 'composer';
			$sharedStoragePath = base_path('storage_bkp');
		} else {
			$php = '/opt/plesk/php/7.2/bin/php';
			$composer = "{$php} /usr/lib64/plesk-9.0/composer.phar";
			$sharedStoragePath = '/var/www/vhosts/plaisure.com/storage/';
		}
		$storagePath = storage_path();

		// Setup commands
		$processes = [
			new Process([$composer, 'help']),
			new Process(['rm', '-rf', $storagePath]),
			new Process(['ln', '-s', $sharedStoragePath, $storagePath]),
			new Process([$php, 'artisan', 'migrate', '--force'])
		];

		try {
			// Run commands
			foreach ($processes as $process) {
				$this->info("Running {$process->getCommandLine()}...");
				$process->mustRun();
				$this->line($process->getOutput());
			}
		} catch (ProcessFailedException $exception) {
			$this->error($exception->getMessage());
		}
	}
}
