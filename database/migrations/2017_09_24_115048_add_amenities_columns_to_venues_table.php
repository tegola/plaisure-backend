<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmenitiesColumnsToVenuesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('venues', function(Blueprint $table) {
			$table->boolean('amenity_atm')            ->after('url_tripadvisor');
			$table->boolean('amenity_bar')            ->after('amenity_atm');
			$table->boolean('amenity_pay_per_view')   ->after('amenity_bar');
			$table->boolean('amenity_pos')            ->after('amenity_pay_per_view');
			$table->boolean('amenity_private_parking')->after('amenity_pos');
			$table->boolean('amenity_restaurant')     ->after('amenity_private_parking');
			$table->boolean('amenity_security')       ->after('amenity_restaurant');
			$table->boolean('amenity_smoking_area')   ->after('amenity_security');
			$table->boolean('amenity_wifi')           ->after('amenity_smoking_area');
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
			$table->dropColumn([
				'amenity_atm',
				'amenity_bar',
				'amenity_pay_per_view',
				'amenity_pos',
				'amenity_private_parking',
				'amenity_restaurant',
				'amenity_security',
				'amenity_smoking_area',
				'amenity_wifi'
			]);
		});
	}
}
