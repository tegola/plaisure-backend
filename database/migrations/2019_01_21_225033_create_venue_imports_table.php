<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVenueImportsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('venue_imports', function (Blueprint $table) {
			$table->increments('id');

			$table->integer('source_brand');
			$table->string('source_id');
			$table->text('source_data'); // JSON not supported on MariaDB
			$table->text('normalized_data'); // JSON not supported on MariaDB

			$table->index(['source_brand', 'source_id']);

			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('venue_imports');
	}
}
