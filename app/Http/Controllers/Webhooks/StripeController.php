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
	/*
	protected function handleInvoiceUpcoming(array $payload)
	{
		$payload = json_decode(json_encode($payload)); // Array to object
		$invoice = $payload->data->object;

		// Get user and subscription
		$invoice->customer = 'cus_EnKSgANzo3yERV'; // FIXME: remove
		$invoice->subscription = 'sub_EnKTs7FVbHAUS5'; // FIXME: remove
		$user = $this->getUserByStripeId($invoice->customer);
		$subscription = $this->getSubscriptionByStripeId($invoice->subscription);

		if ($user && $subscription) {
			// FIXME: Eccezione se non vengon trovati?
			$user->notify(new BillingUpcomingNotification($invoice, $subscription));
		}

		return new Response('Webhook Handled', 200);
	}
	*/

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
