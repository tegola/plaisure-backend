<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConvertBackVenuePlansConfigToNormalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('venue_plans', function (Blueprint $table) {
            $table->dropColumn('config');

            $table->integer('distance_bonus')->unsigned()->after('machine_name');
            $table->integer('photo_limit')->unsigned()->after('distance_bonus');
            $table->boolean('hide_nearby_venues')->after('photo_limit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('venue_plans', function (Blueprint $table) {
            $table->dropColumn([
                'distance_bonus',
                'photo_limit',
                'hide_nearby_venues'
            ]);

            $table->text('config')->after('machine_name'); // JSON not supported on MariaDB
        });
    }
}
