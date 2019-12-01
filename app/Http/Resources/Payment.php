<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Stripe\PaymentIntent as StripePaymentIntent;

class Payment extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function toArray($request)
	{
		return [
			'currency' => $this->currency,
			'amount' => $this->amount / 100, // 3900 -> 39
			'client_secret' => $this->client_secret,
			'requires_action' => $this->status === StripePaymentIntent::STATUS_REQUIRES_ACTION,
			'is_cancelled' => $this->status === StripePaymentIntent::STATUS_CANCELED,
			'is_succeded' => $this->status === StripePaymentIntent::STATUS_SUCCEEDED
		];
	}
}
