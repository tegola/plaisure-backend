<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVenuePlansTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('venue_plans', function (Blueprint $table) {
			$table->increments('id');

			// Venue relationship
			$table->integer('venue_id')->unsigned()->nullable();
			$table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');

			// Name
			$table->string('name');
			$table->string('machine_name');

			// Bonuses and limits
			$table->integer('distance_bonus')->unsigned();
			$table->integer('photo_limit')->unsigned();
			$table->boolean('hide_nearby_venues');

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
		Schema::dropIfExists('venue_plans');
	}
}
