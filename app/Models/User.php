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
	 * Create a new User model instance.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	public function __construct(array $attributes = [])
	{
		// Default country
		$this->country = env('APP_COUNTRY');

		parent::__construct($attributes);
	}

	/**
	 * Fills the model's properties with the source from Stripe.
	 *
	 * @param  \Stripe\Card|\Stripe\BankAccount|null  $card
	 * @return $this
	 */
	protected function fillCardDetails($card)
	{
	    if ($card instanceof StripeCard) {
	        $this->card_brand = $card->brand;
	        $this->card_last_four = $card->last4;
	        $this->card_expiry_month = $card->exp_month;
	        $this->card_expiry_year = $card->exp_year;
	        $this->card_holder_name = $card->name;
	    } elseif ($card instanceof StripeBankAccount) {
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
}
