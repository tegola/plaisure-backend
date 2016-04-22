<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
	return [
		'name' => $faker->name,
		'email' => $faker->email,
		'password' => bcrypt(str_random(10)),
		'remember_token' => str_random(10),
	];
});


$factory->define(App\Venue::class, function (Faker\Generator $faker) {
	return [
		// AAMS data
		'aams_census_code' => $faker->unique()->ean13,
		'aams_subject_enrollment_code' => $faker->unique()->ean13,

		// Name and features
		'name' => $faker->company,
		'type' => 'non pervenuto',
		'surface_size' => $faker->randomNumber(3),
		'machine_type' => 'A/B',

		'address_street' => $faker->streetName,
		'address_number' => $faker->buildingNumber,
		'address_city' => $faker->city,
		'address_postcode' => $faker->postcode,
		'address_province' => $faker->city, // Faker doesn't provide provinces
		'address_region' => $faker->city, // Faker doesn't provide regions
		'address_country' => $faker->country,

		'geo_latitude' => $faker->randomFloat(6, 41, 43), // $faker->latitude(42, 42),
		'geo_longitude' => $faker->randomFloat(6, 13, 15) // $faker->longitude(14, 14)
	];
});