<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PortCategoriesTablesToVenueCategoriesTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Rename categories table
		Schema::rename('categories', 'venue_categories');

		// Rename category_venue table
		Schema::rename('category_venue', 'venue_venue_category');
		
		// Rename category_id to venue_category_id and reassign foreigns
		Schema::table('venue_venue_category', function (Blueprint $table) {
			// Drop foreign for the old table name
			$table->dropForeign('category_venue_venue_id_foreign');
			$table->dropForeign('category_venue_category_id_foreign');

			// Rename column
			$table->renameColumn('category_id', 'venue_category_id')->unsigned()->index();

			// Restore foreigns for the new table name
			$table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
			$table->foreign('venue_category_id')->references('id')->on('venue_categories')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Restore categories table
		Schema::rename('venue_categories', 'categories');

		// Restore category_venue table
		Schema::rename('venue_venue_category', 'category_venue');

		// Restore category_id from venue_category_id and reassign foreigns
		Schema::table('category_venue', function (Blueprint $table) {
			// Drop foreign for the old table name
			$table->dropForeign('venue_venue_category_venue_id_foreign');
			$table->dropForeign('venue_venue_category_venue_category_id_foreign');

			// Rename column
			$table->renameColumn('venue_category_id', 'category_id')->unsigned()->index();

			// Restore foreigns for the new table name
			$table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
			$table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
		});
	}
}

