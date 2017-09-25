<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConcessionaireIdFieldAndRelationshipToVenuesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('venues', function(Blueprint $table) {
			$table->integer('concessionaire_id')->unsigned()->nullable()->after('owner_id');
			$table->foreign('concessionaire_id')->references('id')->on('concessionaires')->onDelete('set null');
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
			$table->dropForeign('venues_concessionaire_id_foreign');
			$table->dropColumn('concessionaire_id');
		});
	}
}
