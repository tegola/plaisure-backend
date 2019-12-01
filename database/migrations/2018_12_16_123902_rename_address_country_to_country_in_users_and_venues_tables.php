<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAddressCountryToCountryInUsersAndVenuesTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function (Blueprint $table) {
			$table->renameColumn('address_country', 'country');
		});
		Schema::table('venues', function (Blueprint $table) {
			$table->renameColumn('address_country', 'country');
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
			$table->renameColumn('country', 'address_country');
		});
		Schema::table('venues', function (Blueprint $table) {
			$table->renameColumn('country', 'address_country');
		});
	}
}
