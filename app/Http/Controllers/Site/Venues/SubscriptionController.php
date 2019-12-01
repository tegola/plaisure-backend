<?php

namespace App\Http\Controllers\Site\Venues;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payment as PaymentResource;
use App\Http\Resources\Subscription as SubscriptionResource;
use App\Http\Resources\Venue as VenueResource;
use App\Models\Subscription;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Invoice as StripeInvoice;

class SubscriptionController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:api');
		$this->middleware(function($request, $next) {
			$this->authorize('update', $request->venue);

			return $next($request);
		});
	}

	/**
	 * Load data for the venue subscription page.
	 *
	 * @param  Venue  $venue
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Venue $venue, Request $request) {
		$user = auth()->user();

		// Create a payment intent in case the user wants to update its
		// payment method
		$paymentIntent = $user->createSetupIntent();

		// Load venue subscriptions
		$venue->load('subscriptions');

		return [
			'venue' => new VenueResource($venue),
			'paymentIntentSecret' => $paymentIntent->client_secret
		];
	}

	/**
	 * Update the subscription for the specified venue.
	 *
	 * @param  Venue  $venue
	 * @param  Request $request
	 * @return Illuminate\Http\Response
	 */
	public function update(Venue $venue, Request $request)
	{
		$user = auth()->user();
		$subscription = $venue->subscription();
		$subscriptionName = $request->subscription_name;

		// Prepare basic validations
		$rules = [
			'subscription_name' => [
				'nullable',
				Rule::in(array_keys(config('subscriptions'))) // Only valid subscriptions
			]
		];

		// Additional validations based on subscription name and user status
		if ($subscriptionName) {
			// Require billing data if something is missing or user wants to
			// use new billing info
			if (!$user->hasBillingInfo() || $request->new_billing) {
				$rules = array_merge($rules, [
					'legal_name' => 'required|string',
					'address_line1' => 'required|string',
					'address_line2' => 'nullable|string',
					'address_city' => 'required|string',
					'address_region' => 'required|string',
					'address_postcode' => 'required|string',
					'country' => 'required|string',
					'vat_number' => 'required|string|max:20'
				]);
			}

			// Require payment method and credit card holder if there isn't a
			// payment method registered or user wants to use a new one
			if (!$user->hasPaymentMethod() || $request->new_payment) {
				$rules = array_merge($rules, [
					'payment_method_id' => 'required',
					// 'card_holder_name' => 'required' // FIXME: serve ancora?
				]);
			}
		}

		// Validate fields
		$request->validate($rules);

		// Set new subscription
		if (!$subscriptionName) {

			// No subscription selected: if one is present, cancel it at the end
			// of the period (if active) or cancel it right now (if incomplete)
			if ($subscription) {
				if ($subscription->active()) {
					$subscription->cancel();
				} else {
					$subscription->cancelNow();
				}
			}

		} else {

			// Paid subscription
			$subscriptionConfig = $this->getSubscriptionConfig($subscriptionName, $venue);
			$planId = app()->isLocal() ? $subscriptionConfig['stripe_test_plan'] : $subscriptionConfig['stripe_plan'];

			if ($subscription && $subscription->valid()) {
				// Already subscribed: resume current subscription (if it's the
				// same) or swap it with the new one
				if ($subscriptionName == $subscription->name) {
					if ($subscription->onGracePeriod()) {
						$subscription->resume();
					}
				} else {
					$subscription->swap($planId);
				}
			} else {
				// Not subscribed, create the new subscription
				$paymentMethod = !$user->hasPaymentMethod() || $request->new_payment
					? $request->payment_method_id
					: $user->defaultPaymentMethod()->id;

				try {
					$subscription = $user
						->newSubscription($subscriptionName, $planId)
						->withMetadata([
							'user_id' => $user->id,
							'venue_id' => $venue->id
						])
						->create($paymentMethod);
				} catch (IncompletePayment $e) {
					// Since the exception doesn't return the subscription (but
					// actually creates it), retrieve it from the payment invoice
					$invoice = StripeInvoice::retrieve($e->payment->invoice, Cashier::stripeOptions());
					$subscription = Subscription::where('stripe_id', $invoice->subscription)->firstOrFail();
				}
			}

			// Update subscription data
			$subscription->venue_id = $venue->id;
			$subscription
				->fill(Arr::only($subscriptionConfig, [
					'name',
					'currency',
					'price',
					'distance_bonus',
					'hide_nearby_venues',
					'home_page_highlight'
				]))
				->save();

		}

		// Store billing and payment
		if (!$subscription->cancelled()) {
			// Store billing info if needed or user wanted new ones
			if (!$user->hasBillingInfo() || $request->new_billing) {
				$user->update([
					'legal_name' => $request->legal_name,
					'address_line1' => $request->address_line1,
					'address_line2' => $request->address_line2 ?: '',
					'address_city' => $request->address_city,
					'address_postcode' => $request->address_postcode,
					'address_region' => $request->address_region,
					'country' => $request->country,
					'vat_number' => $request->vat_number
				]);
				$user->updateStripeCustomer();
			}

			// Update payment info if subscription is complete and user needs to
			// store it or wanted a new one
			if (!$subscription->hasIncompletePayment() && (!$user->hasPaymentMethod() || $request->new_payment)) {
				$user->updateDefaultPaymentMethodFromStripe();
			}
		}

		return [
			'subscription' => new SubscriptionResource($subscription)
		];
	}

	/**
	 * Returns the subscription configuration for the specified Venue, by
	 * querying the venue country or user country.
	 *
	 * @param  string $name
	 * @param  Venue  $venue
	 * @return array
	 */
	private function getSubscriptionConfig(string $name, Venue $venue)
	{
		$user = auth()->user();
		$userCountry = $user->locale ? locale_get_region($user->locale) : null;
		$config = config("subscriptions.{$name}");
		$baseConfig = data_get($config, 'base');
		$countryConfig = data_get($config, $venue->country, []);

		/*
		if ($venue->country && array_key_exists($venue->country, $config)) {
			$countryConfig = $config[$venue->country];
		} else if ($userCountry && array_key_exists($userCountry, $config)) {
			$countryConfig = $config[$userCountry];
		} else {
			$countryConfig = [];
		}
		*/

		return array_merge($baseConfig, $countryConfig);
	}
}