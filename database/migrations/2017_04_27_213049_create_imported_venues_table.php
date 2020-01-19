<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportedVenuesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('imported_venues', function (Blueprint $table) {
			$table->increments('id');

			// AAMS data
			$table->string('aams_census_code');
			$table->string('aams_subject_enrollment_code');

			// Name and type
			$table->string('name');
			$table->string('type');

			// Features
			$table->float('surface_size')->unsigned();
			$table->integer('machine_type')->unsigned();

			// Address
			$table->string('address_1');
			$table->string('address_2');

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
		Schema::dropIfExists('imported_venues');
	}
}
