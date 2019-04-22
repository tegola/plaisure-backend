<?php
return [
	// Default subscription ---------------------------------------------------
	// Keep it forever, it's used throughout the app
	'default' => [
		'base' => [
			'name' => 'default',
			'currency' => 'EUR',
			'price' => 0,
			'stripe_plan' => null,
			'stripe_test_plan' => null,
			'distance_bonus' => 0,
			'photo_limit' => 20, // FIXME: Rimuovere
			'hide_nearby_venues' => false
		],
		'IT' => [],
		'GB' => [
			'currency' => 'GBP'
		]
	],

	// Paid subscriptions -----------------------------------------------------
	'premium_1' => [
		'base' => [
			'name' => 'premium_1',
			'currency' => 'EUR',
			'price' => 39,
			'distance_bonus' => 10,
			'photo_limit' => 50, // FIXME: Rimuovere
			'hide_nearby_venues' => false,
		],
		'IT' => [
			'stripe_plan' => 'plan_Ev9mvmxP7zaX9l',
			'stripe_test_plan' => 'plan_DhbCoKimOWGLU4'
		],
		'GB' => [
			'currency' => 'GBP',
			'stripe_plan' => 'plan_Ev9nsDjfHxCcJI',
			'stripe_test_plan' => 'plan_Ev7HEaY6XV8OZQ'
		]
	],

	'premium_2' => [
		'base' => [
			'name' => 'premium_2',
			'currency' => 'EUR',
			'price' => 79,
			'distance_bonus' => 50,
			'photo_limit' => 50, // FIXME: Rimuovere
			'hide_nearby_venues' => true
		],
		'IT' => [
			'stripe_plan' => 'plan_Ev9nEgy0auQELQ',
			'stripe_test_plan' => 'plan_EmwdZ3ELUNjJj3'
		],
		'GB' => [
			'currency' => 'GBP',
			'stripe_plan' => 'plan_Ev9nLU0jqdgcBX',
			'stripe_test_plan' => 'plan_Ev7HuJdLnQvXqi'
		]
	],
];