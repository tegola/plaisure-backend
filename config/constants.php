<?php

return [
	'name' => 'ProntoGioco', // FIXME: Remove?
	'company' => 'PRG s.r.l.',
	'partita_iva' => '02554710695',
	'email' => [
		'generic' => 'info@prontogioco.it',
		'venues' => 'venues@prontogioco.it',
		'report' => 'report@prontogioco.it',
	],

	'venues_csv_path' => [
		'dirname' => 'csv',
		'filename' => 'esercizi.csv'
	],

	'search_default_distance' => 5, // FIXME: Needed?

	'google_maps_api_key' => 'AIzaSyC7HUu36wqXlH_E27AMOFFF9v7t1809Upk',

	'footer_explore_cities' => [
		'Milano' => [
			'c_lat' => 45.462734,
			'c_lng' => 9.177732,
			'ne_lat' => 45.535689,
			'ne_lng' => 9.290346,
			'sw_lat' => 45.389779,
			'sw_lng' => 9.065118,
			'near' => 'Milano, MI'
		],
		'Bologna' => [
			'c_lat' => 44.499118,
			'c_lng' => 11.331685,
			'ne_lat' => 44.556199,
			'ne_lng' => 11.433717,
			'sw_lat' => 44.442038,
			'sw_lng' => 11.229654,
			'near' => 'Bologna, BO'
		],
		'Roma' => [
			'c_lat' => 41.910071,
			'c_lng' => 12.535998,
			'ne_lat' => 42.050546,
			'ne_lng' => 12.730289,
			'sw_lat' => 41.769596,
			'sw_lng' => 12.341707,
			'near' => 'Roma, RM'
		],
		'Napoli' => [
			'c_lat' => 40.85398565,
			'c_lng' => 14.24660234999999,
			'ne_lat' => 40.9159348,
			'ne_lng' => 14.353714800000034,
			'sw_lat' => 40.79203649999999,
			'sw_lng' => 14.139489899999944,
			'near' => 'Napoli, NA'
		],
		'Palermo' => [
			'c_lat' => 38.1404854,
			'c_lng' => 13.357288550000021,
			'ne_lat' => 38.2194316,
			'ne_lng' => 13.447156599999971,
			'sw_lat' => 38.0615392,
			'sw_lng' => 13.267420500000071,
			'near' => 'Palermo, PA'
		]
	]
];