<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConvertStreetWithLine1AndLine2InUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function (Blueprint $table) {
			$table->string('address_line1')->after('remember_token');
			$table->string('address_line2')->after('address_line1');
		});

		$rows = DB::table('users')
			->select('id', 'address_street')
			->get();

		foreach ($rows as $row) {
			DB::table('users')
				->where('id', $row->id)
				->update(['address_line1' => $row->address_street]);
		}

		Schema::table('users', function (Blueprint $table) {
			$table->dropColumn('address_street');
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
			$table->string('address_street')->after('remember_token');
		});

		$rows = DB::table('users')
			->select('id', 'address_line1')
			->get();

		foreach ($rows as $row) {
			DB::table('users')
				->where('id', $row->id)
				->update(['address_street' => $row->address_line1]);
		}

		Schema::table('users', function (Blueprint $table) {
			$table->dropColumn([
				'address_line1',
				'address_line2'
			]);
		});
	}
}
