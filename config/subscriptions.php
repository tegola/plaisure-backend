<?php

return [
	// Default subscription ---------------------------------------------------
	// Keep it forever, is used by the Venue model
	'default' => [
		'name' => 'default',
		'currency' => 'EUR',
		'price' => 0,
		'distance_bonus' => 0,
		'photo_limit' => 5,
		'hide_nearby_venues' => false
	],

	// Paid subscriptions -----------------------------------------------------
	'premium_1' => [
		'name' => 'premium_1',
		'currency' => 'EUR',
		'price' => 39,
		'distance_bonus' => 5,
		'photo_limit' => 20,
		'hide_nearby_venues' => true
	],
	'premium_2' => [
		'name' => 'premium_2',
		'currency' => 'EUR',
		'price' => 79,
		'distance_bonus' => 15,
		'photo_limit' => 50,
		'hide_nearby_venues' => true
	]
];
