<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('files', function (Blueprint $table) {
			$table->increments('id');
			$table->tinyInteger('type')->unsigned();

			// Polymorphic relationship
			$table->string('filable_type');
			$table->integer('filable_id')->unsigned();

			// User relationship (created by)
			$table->integer('user_id')->unsigned()->nullable()->comment('Created by');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

			// File info
			$table->string('name');
			$table->string('extension');
			$table->string('mime_type');
			$table->integer('size')->unsigned();

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
		Schema::dropIfExists('files');
	}
}
