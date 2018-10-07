<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigratePlansTableToCashierSubscriptions extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function ($table) {
			$table->string('stripe_id')->nullable()->after('aams_subject_enrollment_code');
			$table->string('card_brand')->nullable()->after('stripe_id');
			$table->string('card_last_four')->nullable()->after('card_brand');
			$table->timestamp('trial_ends_at')->nullable()->after('card_last_four');
		});

		Schema::create('subscriptions', function ($table) {
			$table->increments('id');

			// User relationship
			$table->unsignedInteger('user_id');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

			// Venue relationship
			$table->unsignedInteger('venue_id')->nullable(); // Nullable so it can be added after Cashier created the record
			$table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');

			$table->string('name');
			$table->string('stripe_id');
			$table->string('stripe_plan');
			$table->string('currency', 3);
			$table->float('price', 8, 2);
			$table->integer('quantity');
			$table->integer('distance_bonus');
			$table->integer('photo_limit');
			$table->boolean('hide_nearby_venues');

			$table->timestamp('trial_ends_at')->nullable();
			$table->timestamp('ends_at')->nullable();
			$table->timestamps();
		});

		Schema::dropIfExists('venue_plans');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('venue_plans', function (Blueprint $table) {
			$table->increments('id');

			// Venue relationship
			$table->integer('venue_id')->unsigned()->nullable();
			$table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');

			// Name
			$table->string('name');
			$table->string('machine_name');
			$table->float('price');

			// Bonuses and limits
			$table->integer('distance_bonus')->unsigned();
			$table->integer('photo_limit')->unsigned();
			$table->boolean('hide_nearby_venues');

			$table->timestamps();
		});

		Schema::dropIfExists('subscriptions');

		Schema::table('users', function (Blueprint $table) {
			$table->dropColumn([
				'stripe_id',
				'card_brand',
				'card_last_four',
				'trial_ends_at'
			]);
		});
	}
}
