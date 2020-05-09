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
		'hide_nearby_venues' => false,
		'home_page_highlight' => false
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'hide_nearby_venues' => 'boolean',
		'home_page_highlight' => 'boolean',
		'current_period_ends_at' => 'datetime'
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

	/**
	 * Get the end of the current period.
	 * 
	 * @param  Carbon $value
	 * @return Carbon
	 */
	public function getCurrentPeriodEndsAtAttribute($value)
	{
		// Return cached value if present
		if ($value) return Carbon::parse($value);

		// Otherwise get value from stripe subscription (and cache it)
		try {
			$stripeSubscription = $this->asStripeSubscription();

			$this->current_period_ends_at = $stripeSubscription->current_period_end;
			$this->save();

			return Carbon::parse($stripeSubscription->current_period_end);
		} catch (\Exception $e) {
			return null;
		}
	}
}
