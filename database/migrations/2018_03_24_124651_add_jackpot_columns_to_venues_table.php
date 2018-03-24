<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJackpotColumnsToVenuesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('venues', function(Blueprint $table) {
			$table->string('jackpot1_label', 30)->after('url_tripadvisor');
			$table->float('jackpot1_value', 10, 2)->after('jackpot1_label');
			$table->string('jackpot2_label', 30)->after('jackpot1_value');
			$table->float('jackpot2_value', 10, 2)->after('jackpot2_label');
			$table->string('jackpot3_label', 30)->after('jackpot2_value');
			$table->float('jackpot3_value', 10, 2)->after('jackpot3_label');
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
			$table->dropColumn([
				'jackpot1_label',
				'jackpot1_value',
				'jackpot2_label',
				'jackpot2_value',
				'jackpot3_label',
				'jackpot3_value'
			]);
		});
	}
}
