<?php

namespace App\Models;

use Laravel\Cashier\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{

	/**
	 * The model's attributes.
	 *
	 * @var array
	 */
	protected $attributes = [
		'currency' => '',
		'price' => 0,
		'distance_bonus' => 0,
		'photo_limit' => 50, // FIXME: Remove
		'hide_nearby_venues' => false
	];

	/**
	 * Get the venue related to the subscription.
	 *
	 * @return \App1\Models\Venue
	 */
	public function venue()
	{
		return $this->belongsTo('App\Models\Venue');
	}
}
