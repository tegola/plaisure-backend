<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLegalAndAddressFieldsToUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function (Blueprint $table) {
			$table->string('legal_name')->after('name');

			$table->string('address_street')->after('remember_token');
			$table->string('address_city')->after('address_street');
			$table->string('address_postcode')->after('address_city');
			$table->string('address_region')->after('address_postcode');
			$table->string('address_country')->after('address_region');

			$table->string('vat_number', 20)->after('address_country');
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
				'legal_name',
				'address_street',
				'address_city',
				'address_postcode',
				'address_region',
				'address_country',
				'vat_number'
			]);
		});
	}
}
