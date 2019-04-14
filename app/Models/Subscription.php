<?php

namespace App\Models;

use Laravel\Cashier\Subscription as CashierSubscription;
use Carbon;

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
	 * @return \App\Models\Venue
	 */
	public function venue()
	{
		return $this->belongsTo('App\Models\Venue');
	}

	public function getCurrentPeriodEndsAtAttribute()
	{
		try {
			$stripeSubscription = $this->asStripeSubscription();

			return Carbon::parse($stripeSubscription->current_period_end);
		} catch (\Exception $e) {
			return null;
		}
	}
}
