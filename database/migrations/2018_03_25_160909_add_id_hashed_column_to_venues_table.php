<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdHashedColumnToVenuesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add column. It has a collation set so it becomes case sensitive,
		// while the entire database is case insensitive. Otherwise, we should
		// have provided Hashids a lowercase alphabet everytime we needed to
		// encode the value.
		Schema::table('venues', function(Blueprint $table) {
			$table
				->string('id_hashed', 10)
				->after('id')
				->collation('utf8mb4_bin')
				->nullable()
				->unique();
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
			$table->dropColumn('id_hashed');
		});
	}
}
