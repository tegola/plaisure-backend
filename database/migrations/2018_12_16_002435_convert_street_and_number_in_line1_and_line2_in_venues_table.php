<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConvertStreetAndNumberInLine1AndLine2InVenuesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('venues', function (Blueprint $table) {
			$table->string('address_line1')->after('address_street');
			$table->string('address_line2')->after('address_number');
		});

		$rows = DB::table('venues')
			->select('id', 'address_street', 'address_number')
			->get();

		foreach ($rows as $row) {
			DB::table('venues')
				->where('id', $row->id)
				->update([
					'address_line1' => trim("{$row->address_street} {$row->address_number}")
				]);
		}

		Schema::table('venues', function (Blueprint $table) {
			$table->dropColumn([
				'address_street',
				'address_number'
			]);
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
			$table->string('address_street')->after('machine_type');
			$table->string('address_number')->after('address_number');
		});

		// Not reversing data since we don't know how to split street address
		// and number

		Schema::table('venues', function (Blueprint $table) {
			$table->dropColumn([
				'address_line1',
				'address_line2'
			]);
		});
	}
}
