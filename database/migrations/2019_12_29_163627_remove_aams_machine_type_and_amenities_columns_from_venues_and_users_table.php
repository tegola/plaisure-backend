<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveAamsMachineTypeAndAmenitiesColumnsFromVenuesAndUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('venues', function (Blueprint $table) {
			$table->dropColumn([
				'aams_census_code',
				'aams_subject_enrollment_code',
				'machine_type',
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

		Schema::table('users', function (Blueprint $table) {
			$table->dropColumn('aams_subject_enrollment_code');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	}
}
