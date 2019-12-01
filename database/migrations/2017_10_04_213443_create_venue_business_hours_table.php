<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVenueBusinessHoursTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('venue_business_hours', function (Blueprint $table) {
			// Venue relationship
			$table->integer('venue_id')->unsigned()->nullable();
			$table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');

			// Fields
			$table->tinyInteger('day')->unsigned()->nullable();
			$table->time('opens');
			$table->time('closes');
			$table->date('valid_from')->nullable();
			$table->date('valid_to')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('venue_business_hours');
	}
}
