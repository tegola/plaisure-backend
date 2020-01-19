<?php

namespace App\Http\Controllers\Webhooks;

use Symfony\Component\HttpFoundation\Response;
use Laravel\Cashier\Http\Controllers\WebhookController;
use App\Notifications\BillingUpcoming as BillingUpcomingNotification;
use App\Models\Subscription;

class StripeController extends WebhookController
{
	/**
	 * Handle upcoming billing from a Stripe subscription.
	 *
	 * @param  array  $payload
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function handleInvoiceUpcoming(array $payload)
	{
		$payload = json_decode(json_encode($payload)); // Array to object
		$invoice = $payload->data->object;
		$user = $this->getUserByStripeId($invoice->customer);
		$subscription = $this->getSubscriptionByStripeId($invoice->subscription);

		if ($user && $subscription) {
			$stripeSubscription = $subscription->asStripeSubscription();

			// Store end of current period
			$subscription->current_period_ends_at = $stripeSubscription->current_period_end;
			$subscription->save();

			// Notify user of upcoming billing
			$user->notify(new BillingUpcomingNotification($subscription));
		}

		return $this->successMethod();
	}

	/**
	 * Get the subscription entity instance by Stripe ID.
	 *
	 * @param  string  $stripeId
	 * @return \Laravel\Cashier\Billable
	 */
	protected function getSubscriptionByStripeId($stripeId)
	{
		return Subscription::where('stripe_id', $stripeId)->first();
	}
}
