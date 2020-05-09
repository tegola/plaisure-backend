<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('visits', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('venue_id')->unsigned()->index();
			$table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
			$table->date('date');
			$table->integer('count');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('visits');
	}
}
