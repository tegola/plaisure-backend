<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVenueImportIdFieldToVenuesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('venues', function (Blueprint $table) {
			// Venue import relationship
			$table->integer('venue_import_id')->unsigned()->nullable()->after('id_hashed');
			$table->foreign('venue_import_id')->references('id')->on('venue_imports')->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('venues', function (Blueprint $table) {
			$table->dropForeign('venues_venue_import_id_foreign');
			$table->dropColumn('venue_import_id');
		});
	}
}
