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
		Schema::rename('categories', 'venue_categories');
		Schema::rename('category_venue', 'venue_venue_category');
		Schema::table('venue_venue_category', function (Blueprint $table) {
			$table->renameColumn('category_id', 'venue_category_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::rename('venue_categories', 'categories');
		Schema::rename('venue_venue_category', 'category_venue');
		Schema::table('category_venue', function (Blueprint $table) {
			$table->renameColumn('venue_category_id', 'category_id');
		});
	}
}
