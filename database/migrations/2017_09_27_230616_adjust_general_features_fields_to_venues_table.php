<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdjustGeneralFeaturesFieldsToVenuesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('venues', function(Blueprint $table) {
			$table->renameColumn('machine_count', 'vlt_machine_count');
		});
		Schema::table('venues', function(Blueprint $table) {
			$table->integer('awp_machine_count')->unsigned()->after('vlt_machine_count');
			$table->integer('seating_capacity')->unsigned() ->after('awp_machine_count');
			$table->integer('parking_capacity')->unsigned() ->after('seating_capacity');
			$table->boolean('sports_betting')               ->after('parking_capacity');
			$table->boolean('virtual_betting')              ->after('sports_betting');
			$table->boolean('horse_betting')                ->after('virtual_betting');
			$table->boolean('arcade_roulette')              ->after('horse_betting');
			$table->text('url_online_casino')               ->after('url_site');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('venues', function(Blueprint $table) {
			$table->dropColumn([
				'awp_machine_count',
				'seating_capacity',
				'parking_capacity',
				'sports_betting',
				'virtual_betting',
				'horse_betting',
				'arcade_roulette',
				'url_online_casino'
			]);
			$table->renameColumn('vlt_machine_count', 'machine_count');
		});
	}
}
