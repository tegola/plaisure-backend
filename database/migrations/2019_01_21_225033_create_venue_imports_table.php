<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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

			// Venue relationship
			$table->integer('venue_id')->unsigned()->nullable();
			$table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');

			$table->integer('source_brand');
			$table->string('source_id');
			$table->text('source_data'); // JSON not supported on MariaDB

			$table->index(['source_brand', 'source_id']);

			$table->timestamps();
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
