<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryFieldToConcessionairesPayPerViewPlatformsVenueCategoriesAndVltPlatformsTables extends Migration
{
	private $tables = [
		'concessionaires',
		'pay_per_view_platforms',
		'venue_categories',
		'vlt_platforms'
	];

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		foreach ($this->tables as $table) {
			Schema::table($table, function (Blueprint $table) {
				$table->string('country')->after('name');
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		foreach ($this->tables as $table) {
			Schema::table($table, function (Blueprint $table) {
				$table->dropColumn('country');
			});
		}
	}
}
