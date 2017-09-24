<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVenuesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('venues', function (Blueprint $table) {
			$table->increments('id');

			// Owner relationship
			$table->integer('owner_id')->unsigned()->nullable();
			$table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');

			// AAMS data
			$table->string('aams_census_code');
			$table->string('aams_subject_enrollment_code');

			// Name and features
			$table->string('name');
			$table->float('surface_size')->unsigned();
			$table->integer('machine_count')->unsigned();
			$table->integer('machine_type')->unsigned();

			// Address
			$table->string('address_street');
			$table->string('address_number');
			$table->string('address_city');
			$table->string('address_postcode');
			$table->string('address_province');
			$table->string('address_region');
			$table->string('address_country');

			// Geo data
			// Best practice: https://developers.google.com/maps/articles/phpsqlsearch_v3?csw=1
			$table->float('geo_latitude', 10, 6)->nullable();
			$table->float('geo_longitude', 10, 6)->nullable();

			// Contact
			$table->string('contact_phone');
			$table->string('contact_email');
			$table->string('contact_facebook');
			$table->string('contact_twitter');

			// URLs
			$table->text('url_site');
			$table->text('url_facebook');
			$table->text('url_tripadvisor');

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
		Schema::dropIfExists('venues');
	}
}
