<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPathTokenAndCaptionFieldsToFilesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('files', function(Blueprint $table) {
			$table->string('token', 5)->after('user_id');
			$table->text('path')->after('token');
			$table->string('caption')->after('size');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('files', function (Blueprint $table) {
			$table->dropColumn(['token', 'path', 'caption']);
		});
	}
}
