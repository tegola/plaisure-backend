<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentPeriodEndColumnToSubscriptionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('subscriptions', function (Blueprint $table) {
			$table->timestamp('current_period_ends_at')->nullable()->after('trial_ends_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('subscriptions', function (Blueprint $table) {
			$table->dropColumn('current_period_ends_at');
		});
	}
}
