<?php

return [
	// Default plan -----------------------------------------------------------
	// Keep it first, is used by the Venue model
	[
		'name' => 'Free',
		'machine_name' => 'free',
		'price' => 0,
		'distance_bonus' => 0,
		'photo_limit' => 5,
		'hide_nearby_venues' => false
	],

	// Paid plans -------------------------------------------------------------
	[
		'name' => 'Premium 1',
		'machine_name' => 'premium_1',
		'price' => 39,
		'distance_bonus' => 5,
		'photo_limit' => 20,
		'hide_nearby_venues' => true
	],
	[
		'name' => 'Premium 2',
		'machine_name' => 'premium_2',
		'price' => 69,
		'distance_bonus' => 15,
		'photo_limit' => 50,
		'hide_nearby_venues' => true
	]
];
