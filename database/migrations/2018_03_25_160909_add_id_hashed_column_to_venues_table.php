<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Models\Venue;
use Hashids\Hashids;
use Illuminate\Support\Facades\DB;

class AddIdHashedColumnToVenuesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add column. It has a collation set so it becomes case sensitive,
		// while the entire database is case insensitive. Otherwise, we should
		// have provided Hashids a lowercase alphabet everytime we needed to
		// encode the value.
		Schema::table('venues', function(Blueprint $table) {
			$table
				->string('id_hashed', 10)
				->after('id')
				->collation('utf8mb4_bin')
				->nullable()
				->unique();
		});

		// Update existing venues
		$console = new ConsoleOutput();
		$hasher = new Hashids(Venue::class, 10);

		$console->write('Updating existing venues with hashed IDs... ');

		$rows = DB::table('venues')->get(['id']);
		foreach ($rows as $row) {
			DB::table('venues')
				->where('id', $row->id)
				->update(['id_hashed' => $hasher->encode($row->id)]);
		}

		$console->write('Done!');
		$console->writeln('');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('venues', function(Blueprint $table) {
			$table->dropColumn('id_hashed');
		});
	}
}
