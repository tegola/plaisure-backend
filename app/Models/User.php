<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Laravel\Cashier\Billable;
use Illuminate\Contracts\Translation\HasLocalePreference;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable implements HasLocalePreference
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
	 * Get the preferred locale of this user.
	 * 
	 * @return string|null
	 */
	public function preferredLocale()
	{
		return $this->locale
			? locale_get_primary_language($this->locale)
			: null;
	}

	/**
	 * Fills the model's properties with the payment method from Stripe.
	 *
	 * @param  \Laravel\Cashier\PaymentMethod|\Stripe\PaymentMethod|null  $paymentMethod
	 * @return $this
	 */
	protected function fillPaymentMethodDetails($paymentMethod)
	{
		if ($paymentMethod->type === 'card') {
			$this->card_brand = $paymentMethod->card->brand;
			$this->card_last_four = $paymentMethod->card->last4;
			$this->card_expiry_month = $paymentMethod->card->exp_month;
			$this->card_expiry_year = $paymentMethod->card->exp_year;
			// $this->card_holder_name = $paymentMethod->card->name; // FIXME: mi sa che non c'è più
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
	 * Venues favorited by this user.
	 *
	 * @return [\App\Models\Venue]
	 */
	public function favorites()
	{
		return $this->belongsToMany('App\Models\Venue', 'user_favorite_venues');
	}

	/**
	 * Get all this user's subscriptions.
	 *
	 * @return [\App\Models\Subscription]
	 */
	public function subscriptions()
	{
		return $this
			->hasMany('App\Models\Subscription', $this->getForeignKey())
			->orderBy('created_at', 'desc');
	}

	/**
	 * Get all reviews added by this user.
	 * 
	 * @return [\App\Models\Review]
	 */
	public function reviews()
	{
		return $this->hasMany('App\Models\Review');
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
		$customer->preferred_locales = $this->preferredLocale()
			? [$this->preferredLocale()]
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
