<?php

namespace App\Http\Controllers\Site\Venues;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Venue;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Validator;

class SubscriptionController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:api');
	}

	/**
	 * Get the data to show the venue claim page.
	 * 
	 * @param  Venue  $venue
	 * @return Illuminate\Http\Response
	 */
	public function update(Venue $venue, Request $request) {
		$this->authorize('update', $venue);

		$user = auth()->user();
		$subscription = $venue->subscribed() ? $venue->subscription() : null;
		$subscriptions = config('subscriptions');
		$subscriptionName = $request->input('subscription_name');
		$subscriptionConfig = $subscriptions[$subscriptionName];
		$planName = $subscriptionConfig['stripe_plan'];
		$validSubscriptionNames = array_keys($subscriptions);
		$invalidSubscriptionNames = $subscription ? [$subscription->name] : [];

		// Prepare basic validations
		$validator = Validator::make($request->all(), [
			'subscription_name' => [
				'required',
				Rule::in($validSubscriptionNames), // Only valid subscriptions
				Rule::notIn($invalidSubscriptionNames) // Don't allow picking the current subscription
			]
		]);

		// Additional validations based on subscription name and user status
		if ($subscriptionName != 'default') {
			// Require token and credit card holder if there isn't a card registered
			if (!$user->hasCardOnFile()) {
				$validator->addRules([
					'token_id' => 'required',
					'card_holder_name' => 'required'
				]);
			}

			// Require billing data if something is missing
			if (!$user->hasBillingInfo()) {
				$validator->addRules([
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
		}

		// Validate
		$validator->validate();

		// Set new subscription
		if ($subscriptionName == 'default') {

			// Default subscription: just cancel the current subscription if
			// there is one set
			if ($venue->subscribed()) {
				$subscription->cancel();
			}

		} else {

			// Premium subscription: if already subscribed, resume it if it's
			// the same as the current one, or swap it with the new one.
			// If not subscribed, create the new subscription
			if ($venue->subscribed()) {
				if ($subscriptionName == $subscription->name) {
					if ($subscription->onGracePeriod()) {
						$subscription->resume();
					}
				} else {
					$subscription->swap($planName);
				}
			} else {
				$subscription = $user
					->newSubscription($subscriptionName, $planName)
					->withMetadata([
						'user_id' => $user->id,
						'venue_id' => $venue->id
					])
					->create($request->input('token_id'));
			}

			// Update subscription data
			$subscription->venue_id = $venue->id;
			$subscription
				->fill(Arr::only($subscriptionConfig, [
					'name',
					'currency',
					'price',
					'distance_bonus',
					'photo_limit',
					'hide_nearby_venues'
				]))
				->save();

		}

		// Store billing info if needed
		// > 1 means we have more than just the new subscription name
		if ($request->count() > 1) {
			$user
				->fill($request->only([
					'legal_name',
					'address_line1',
					'address_line2',
					'address_city',
					'address_region',
					'address_postcode',
					'country',
					'vat_number'
				]))
				->save();
		}
	}
}