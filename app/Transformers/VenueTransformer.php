<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Venue;
use App\Models\VltPlatform;
use App\Models\Subscription;
use App\Models\VenueCategory;
use App\Models\PayPerViewPlatform;
use App\Transformers\FileTransformer;
use Illuminate\Database\Eloquent\Collection;

class VenueTransformer extends TransformerAbstract
{
	/**
	 * List of resources possible to include
	 *
	 * @var array
	 */
	protected $availableIncludes = [
		'business_hours',
		'categories',
		'category_ids',
		'pay_per_view_platforms',
		'pay_per_view_platform_ids',
		'photos',
		'photo_ids',
		'vlt_platforms',
		'vlt_platform_ids',
		'subscription'
	];

	/**
	 * List of resources to automatically include
	 *
	 * @var array
	 */
	protected $defaultIncludes = [];
	
	/**
	 * A Fractal transformer.
	 *
	 * @return array
	 */
	public function transform(Venue $venue)
	{
		return [
			'id' => $venue->id_hashed,
			// 'owner_id' => $venue->owner_id,
			'concessionaire_id' => $venue->concessionaire_id,
			// 'aams_census_code' => '',
			// 'aams_subject_enrollment_code' => '',

			'name' => $venue->name,
			'description' => $venue->description,
			'surface_size' => $venue->surface_size,
			'vlt_machine_count' => $venue->vlt_machine_count,
			'awp_machine_count' => $venue->awp_machine_count,
			'seating_capacity' => $venue->seating_capacity,
			'parking_capacity' => $venue->parking_capacity,
			'sports_betting' => $venue->sports_betting,
			'virtual_betting' => $venue->virtual_betting,
			'horse_betting' => $venue->horse_betting,
			'arcade_roulette' => $venue->arcade_roulette,
			'machine_type' => $venue->machine_type,
			'address' => [
				'line1' => $venue->address_line1,
				'line2' => $venue->address_line2,
				'city' => $venue->address_city,
				'postcode' => $venue->address_postcode,
				'province' => $venue->address_province,
				// 'region' => $venue->address_region,
			],
			'country' => $venue->country,
			'coords' => [
				'lat' => $venue->geo_latitude,
				'lng' => $venue->geo_longitude,
			],
			'contacts' => [
				'phone' => $venue->contact_phone,
				'email' => $venue->contact_email,
				'facebook' => $venue->contact_facebook,
				'twitter' => $venue->contact_twitter,
			],
			'urls' => [
				'site' => $venue->url_site,
				'online_casino' => $venue->url_online_casino,
				'facebook' => $venue->url_facebook,
				// 'tripadvisor' => $venue->url_tripadvisor,
			],
			'jackpots' => [
				'1' => [
					'label' => $venue->jackpot1_label,
					'value' => $venue->jackpot1_value,
				],
				'2' => [
					'label' => $venue->jackpot2_label,
					'value' => $venue->jackpot2_value,
				],
				'3' => [
					'label' => $venue->jackpot3_label,
					'value' => $venue->jackpot3_value,
				]
			],
			'amenities' => [
				'atm' => $venue->amenity_atm,
				'bar' => $venue->amenity_bar,
				'pay_per_view' => $venue->amenity_pay_per_view,
				'pos' => $venue->amenity_pos,
				'private_parking' => $venue->amenity_private_parking,
				'restaurant' => $venue->amenity_restaurant,
				'security' => $venue->amenity_security,
				'smoking_area' => $venue->amenity_smoking_area,
				'wifi' => $venue->amenity_wifi
			],
			'distance' => $venue->distance,
			'has_owner' => $venue->has_owner,
			'created_at' => (string) $venue->created_at
		];
	}

	/**
	 * Include business hours grouped by day.
	 * [
	 *   0 => ['10:00', '16:00'],
	 *   1 => ['10:00', '13:00', '16:00', '20:00'],
	 *   ...
	 * ]
	 *
	 * @param Venue $venue
	 * @return \League\Fractal\Resource\Collection
	 */
	public function includeBusinessHours(Venue $venue)
	{
		return $this->item($venue->businessHours, function(Collection $businessHours) {
			if (!count($businessHours)) return [];

			$days = [[], [], [], [], [], [], []];

			// Copy business hours in every day
			foreach ($businessHours as $hours) {
				array_push($days[$hours->day], $hours->opens, $hours->closes);
			}

			return $days;
		});
	}

	/**
	 * Include categories.
	 *
	 * @param Venue $venue
	 * @return \League\Fractal\Resource\Collection
	 */
	public function includeCategories(Venue $venue)
	{
		return $this->collection($venue->categories, function(VenueCategory $category) {
			return $category->only('id', 'machine_name', 'name');
		});
	}

	/**
	 * Include category ids.
	 *
	 * @param Venue $venue
	 * @return \League\Fractal\Resource\Item
	 */
	public function includeCategoryIds(Venue $venue)
	{
		return $this->item($venue->categories, function(Collection $categories) {
			return $categories->pluck('id')->all();
		});
	}

	/**
	 * Include pay per view platforms.
	 *
	 * @param Venue $venue
	 * @return \League\Fractal\Resource\Collection
	 */
	public function includePayPerViewPlatforms(Venue $venue)
	{
		return $this->collection($venue->payPerViewPlatforms, function (PayPerViewPlatform $payPerViewPlatform) {
			return $payPerViewPlatform->only('id', 'name');
		});
	}

	/**
	 * Include pay per view platform ids.
	 *
	 * @param Venue $venue
	 * @return \League\Fractal\Resource\Item
	 */
	public function includePayPerViewPlatformIds(Venue $venue)
	{
		return $this->item($venue->payPerViewPlatforms, function(Collection $payPerViewPlatforms) {
			return $payPerViewPlatforms->pluck('id')->all();
		});
	}

	/**
	 * Include photos.
	 *
	 * @param Venue $venue
	 * @return \League\Fractal\Resource\Collection
	 */
	public function includePhotos(Venue $venue)
	{
		return $this->collection($venue->photos, new FileTransformer());
	}

	/**
	 * Include photo ids.
	 *
	 * @param Venue $venue
	 * @return \League\Fractal\Resource\Item
	 */
	public function includePhotoIds(Venue $venue)
	{
		return $this->item($venue->photos, function(Collection $photos) {
			return $photos->pluck('id')->all();
		});
	}

	/**
	 * Include VLT platforms.
	 *
	 * @param Venue $venue
	 * @return \League\Fractal\Resource\Collection
	 */
	public function includeVltPlatforms(Venue $venue)
	{
		return $this->collection($venue->vltPlatforms, function(VltPlatform $vltPlatform) {
			return $vltPlatform->only('id', 'name');
		});
	}

	/**
	 * Include VLT platform ids.
	 *
	 * @param Venue $venue
	 * @return \League\Fractal\Resource\Item
	 */
	public function includeVltPlatformIds(Venue $venue)
	{
		return $this->item($venue->vltPlatforms, function(Collection $vltPlatforms) {
			return $vltPlatforms->pluck('id')->all();
		});
	}

	/**
	 * Include the active subscription.
	 * 
	 * @param  Venue  $venue
	 * @return \League\Fractal\Resource\Item
	 */
	public function includeSubscription(Venue $venue)
	{
		$subscription = $venue->subscribed() ? $venue->subscription() : null;

		// Use default subscription
		if (!$subscription) {
			$subscription = new Subscription(config('subscriptions.default.base'));
		}

		return $this->item($subscription, function(Subscription $subscription) {
			// Select return keys. On a real subscription, also return the
			// period's end date
			$keys = [
				'name',
				'currency',
				'price',
				'distance_bonus',
				'photo_limit',
				'hide_nearby_venues',
				'ends_at',
				'updated_at'
			];
			if ($subscription->name !== 'default') {
				$keys[] = 'current_period_ends_at';
			}

			return $subscription->only($keys);
		});
	}
}
