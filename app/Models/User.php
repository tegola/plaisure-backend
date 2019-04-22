<?php

namespace App\Models;

use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
	use HasApiTokens, Notifiable, Billable;

	/**
	 * The model's default attributes.
	 *
	 * @var array
	 */
	protected $attributes = [
		'name' => '',
		'legal_name' => '',
		'email' => '',
		'email_verified_at' => null,
		'password' => '',
		'remember_token' => null,
		'locale' => '',
		'address_line1' => '',
		'address_line2' => '',
		'address_city' => '',
		'address_postcode' => '',
		'address_region' => '',
		'country' => '',
		'vat_number' => '',
		'aams_subject_enrollment_code' => '',
		'stripe_id' => null,
		'card_brand' => null,
		'card_last_four' => null,
		'card_expiry_month' => null,
		'card_expiry_year' => null,
		'card_holder_name' => null,
		'trial_ends_at' => null,
		'send_newsletter' => false,
		'is_admin' => false,
		'is_owner' => false
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'send_newsletter' => 'boolean',
		'is_admin' => 'boolean',
		'is_owner' => 'boolean'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token'
	];

	/**
	 * The attributes that are not mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * Fills the model's properties with the source from Stripe.
	 *
	 * @param  \Stripe\Card|\Stripe\BankAccount|null  $card
	 * @return $this
	 */
	protected function fillCardDetails($card)
	{
		if ($card instanceof \Stripe\Card) {
			$this->card_brand = $card->brand;
			$this->card_last_four = $card->last4;
			$this->card_expiry_month = $card->exp_month;
			$this->card_expiry_year = $card->exp_year;
			$this->card_holder_name = $card->name;
		} else if ($card instanceof \Stripe\BankAccount) {
			$this->card_brand = 'Bank Account';
			$this->card_last_four = $card->last4;
			$this->card_expiry_month = null;
			$this->card_expiry_year = null;
			$this->card_holder_name = $card->account_holder_name;
		}

		return $this;
	}

	/**
	 * Venues claimed by this user.
	 * 
	 * @return [\App\Models\Venue]
	 */
	public function venues()
	{
		return $this->hasMany('App\Models\Venue', 'owner_id');
	}

	/**
	 * Get all this user's subscriptions.
	 *
	 * @return [App\Models\Subscription]
	 */
	public function subscriptions()
	{
		return $this
			->hasMany('App\Models\Subscription', $this->getForeignKey())
			->orderBy('created_at', 'desc');
	}

	/**
	 * Send the password reset notification.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
		$this->notify(new ResetPasswordNotification($token));
	}

	/**
	 * Determines if the user has set its billing info.
	 * 
	 * @return boolean
	 */
	public function hasBillingInfo()
	{
		return ($this->legal_name
			&& ($this->address_line1 || $this->address_line2)
			&& $this->address_city
			&& $this->address_postcode
			&& $this->address_region
			&& $this->country
			&& $this->vat_number);
	}

	/**
	 * Updates the customer billing data on Stripe.
	 * 
	 * @return \Stripe\ApiResource
	 */
	public function updateStripeCustomer()
	{
		// Stop if user has no billing info or no Stripe id
		if (!$this->hasStripeId()) return;

		$customer = $this->asStripeCustomer();

		// E-mail
		$customer->email = $this->email;

		// Language
		$customer->preferred_locales = $this->locale
			? [locale_get_primary_language($this->locale)]
			: [];

		// Billing address (is called Shipping on Stripe)
		if ($this->legal_name && $this->address_line1) {
			$customer->shipping = [
				'name' => $this->legal_name,
				'address' => [
					'line1' => $this->address_line1,
					'line2' => optional($this)->address_line2,
					'city' => $this->address_city,
					'postal_code' => $this->address_postcode,
					'state' => $this->address_region,
					'country' => $this->country
				]
			];
		} else {
			$customer->shipping = null;
		}

		// Tax info
		$customer->tax_info = [
			'tax_id' => $this->vat_number ?: null,
			'type' => 'vat'
		];

		return $customer->save();
	}
}
