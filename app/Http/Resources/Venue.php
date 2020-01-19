<?php

namespace App\Http\Resources;

use App\Http\Resources\Amenity as AmenityResource;
use App\Http\Resources\File as FileResource;
use App\Http\Resources\Review as ReviewResource;
use App\Http\Resources\Subscription as SubscriptionResource;
use App\Http\Resources\VenueCategory as VenueCategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Venue extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function toArray($request)
	{
		$reviewsQuery = $this->reviews(); // Loads but doesn't attach reviews to the resource
		$ratings = $reviewsQuery->select('rating')->get();
		$reviewCount = $reviewsQuery->withComment()->count();

		return [
			'id' => $this->id_hashed,
			// 'owner_id' => $this->owner_id,
			'concessionaire_id' => $this->concessionaire_id,

			'name' => $this->name,
			'description' => $this->description,
			'surface_size' => $this->surface_size,
			'vlt_machine_count' => $this->vlt_machine_count,
			'awp_machine_count' => $this->awp_machine_count,
			'seating_capacity' => $this->seating_capacity,
			'parking_capacity' => $this->parking_capacity,
			'sports_betting' => $this->sports_betting,
			'virtual_betting' => $this->virtual_betting,
			'horse_betting' => $this->horse_betting,
			'arcade_roulette' => $this->arcade_roulette,
			'address' => [
				'line1' => $this->address_line1,
				'line2' => $this->address_line2,
				'city' => $this->address_city,
				'postcode' => $this->address_postcode,
				'province' => $this->address_province,
				// 'region' => $this->address_region,
			],
			'country' => $this->country,
			'coords' => [
				'lat' => $this->geo_latitude,
				'lng' => $this->geo_longitude,
			],
			'contacts' => [
				'phone' => $this->contact_phone,
				'email' => $this->contact_email,
				'facebook' => $this->contact_facebook,
				'twitter' => $this->contact_twitter,
			],
			'urls' => [
				'site' => $this->url_site,
				'online_casino' => $this->url_online_casino,
				'facebook' => $this->url_facebook,
				// 'tripadvisor' => $this->url_tripadvisor,
			],
			'jackpots' => [
				'1' => [
					'label' => $this->jackpot1_label,
					'value' => $this->jackpot1_value,
				],
				'2' => [
					'label' => $this->jackpot2_label,
					'value' => $this->jackpot2_value,
				],
				'3' => [
					'label' => $this->jackpot3_label,
					'value' => $this->jackpot3_value,
				]
			],
			'rating' => [
				'1_count' => $ratings->where('rating', 1)->count(),
				'2_count' => $ratings->where('rating', 2)->count(),
				'3_count' => $ratings->where('rating', 3)->count(),
				'4_count' => $ratings->where('rating', 4)->count(),
				'5_count' => $ratings->where('rating', 5)->count(),
				'count' => $ratings->count(),
				'average' => $this->rating()
			],
			'review_count' => $reviewCount,
			'distance' => $this->distance,
			'has_owner' => $this->has_owner,
			'created_at' => $this->created_at,

			'photos' => FileResource::collection($this->whenLoaded('photos')),
			'amenities' => AmenityResource::collection($this->whenLoaded('amenities')),
			'categories' => VenueCategoryResource::collection($this->whenLoaded('categories')),
			'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
			'subscription' => $this->whenLoaded('subscriptions', function() {
				return new SubscriptionResource($this->subscription());
			})
		];
	}
}