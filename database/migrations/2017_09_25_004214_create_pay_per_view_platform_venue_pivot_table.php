<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayPerViewPlatformVenuePivotTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pay_per_view_platform_venue', function (Blueprint $table) {
			$table->integer('pay_per_view_platform_id')->unsigned()->index();
			$table->foreign('pay_per_view_platform_id')->references('id')->on('pay_per_view_platforms')->onDelete('cascade');
			$table->integer('venue_id')->unsigned()->index();
			$table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('pay_per_view_platform_venue');
	}
}
