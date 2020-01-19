<?php
return [
	// Default subscription ---------------------------------------------------
	// Keep it forever, it's used throughout the app
	/*
	'default' => [
		'base' => [
			'name' => 'default',
			'currency' => 'EUR',
			'price' => 0,
			'stripe_plan' => null,
			'stripe_test_plan' => null,
			'distance_bonus' => 0,
			'hide_nearby_venues' => false,
			'home_page_highlight' => false
		],
		'IT' => [],
		'GB' => [
			'currency' => 'GBP'
		]
	],
	*/

	// Paid subscriptions -----------------------------------------------------
	'silver' => [
		'base' => [
			'name' => 'silver',
			'currency' => 'EUR',
			'price' => 39,
			'distance_bonus' => 10,
			'hide_nearby_venues' => false,
			'home_page_highlight' => true
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

	'gold' => [
		'base' => [
			'name' => 'gold',
			'currency' => 'EUR',
			'price' => 79,
			'distance_bonus' => 50,
			'hide_nearby_venues' => true,
			'home_page_highlight' => true
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