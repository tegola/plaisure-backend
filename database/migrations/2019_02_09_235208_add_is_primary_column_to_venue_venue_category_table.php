<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPrimaryColumnToVenueVenueCategoryTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('venue_venue_category', function (Blueprint $table) {
			$table->boolean('is_primary')->default(false);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('venue_venue_category', function (Blueprint $table) {
			$table->dropColumn('is_primary');
		});
	}
}
