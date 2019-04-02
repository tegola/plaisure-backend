<?php

return [
	// Default subscription ---------------------------------------------------
	// Keep it forever, it's used throughout the app
	'default' => [
		'name' => 'default',
		'stripe_plan' => null,
		'currency' => 'EUR',
		'price' => 0,
		'distance_bonus' => 0,
		'photo_limit' => 20,
		'hide_nearby_venues' => false
	],

	// Paid subscriptions -----------------------------------------------------
	'premium_1' => [
		'name' => 'premium_1',
		'stripe_plan' => 'plan_DhbCoKimOWGLU4', // FIXME: Specificarlo per ogni environment
		'currency' => 'EUR',
		'price' => 39,
		'distance_bonus' => 10,
		'photo_limit' => 50, // FIXME: Rimuovere
		'hide_nearby_venues' => false
	],
	'premium_2' => [
		'name' => 'premium_2',
		'stripe_plan' => 'plan_EmwdZ3ELUNjJj3', // FIXME: Specificarlo per ogni environment
		'currency' => 'EUR',
		'price' => 79,
		'distance_bonus' => 50,
		'photo_limit' => 50, // FIXME: Rimuovere
		'hide_nearby_venues' => true
	]
];
