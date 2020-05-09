<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Subscription extends JsonResource
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
			'name' => $this->name,
			'currency' => $this->currency,
			'price' => $this->price,
			'distance_bonus' => $this->distance_bonus,
			'hide_nearby_venues' => $this->hide_nearby_venues,
			'home_page_highlight' => $this->home_page_highlight,
			'payment_pending' => $this->resource->hasIncompletePayment(),
			'is_valid' => $this->resource->valid(),
			'ends_at' => $this->ends_at,
			'current_period_ends_at' => $this->current_period_ends_at,
			'updated_at' => $this->updated_at
		];
	}
}
