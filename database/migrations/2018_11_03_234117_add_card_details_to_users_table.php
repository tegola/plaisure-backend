<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCardDetailsToUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function (Blueprint $table) {
			$table->string('card_expiry_month')->nullable()->after('card_last_four');
			$table->string('card_expiry_year')->nullable()->after('card_expiry_month');
			$table->string('card_holder_name')->nullable()->after('card_expiry_year');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function (Blueprint $table) {
			$table->dropColumn([
				'card_expiry_month',
				'card_expiry_year',
				'card_holder_name'
			]);
		});
	}
}
