<?php

namespace App\Transformers;

use App\Models\Amenity;
use App\Models\Review;
use App\Models\Subscription;
use App\Models\Venue;
use App\Models\VenueCategory;
use App\Models\VltPlatform;
use App\Transformers\FileTransformer;
use Illuminate\Database\Eloquent\Collection;
use League\Fractal\TransformerAbstract;

class VenueTransformer extends TransformerAbstract
{
	/**
	 * List of resources possible to include
	 *
	 * @var array
	 */
	protected $availableIncludes = [
		'business_hours',
		'amenities',
		'amenity_ids',
		'categories',
		'category_ids',
		'photos',
		'photo_ids',
		'vlt_platforms',
		'vlt_platform_ids',
		'subscription',
		'reviews'
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
		$reviewsQuery = $venue->reviews();
		$ratings = $reviewsQuery->select('rating')->get();
		$reviewCount = $reviewsQuery->withComment()->count();

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
			'rating' => [
				'1_count' => $ratings->where('rating', 1)->count(),
				'2_count' => $ratings->where('rating', 2)->count(),
				'3_count' => $ratings->where('rating', 3)->count(),
				'4_count' => $ratings->where('rating', 4)->count(),
				'5_count' => $ratings->where('rating', 5)->count(),
				'count' => $ratings->count(),
				'average' => $venue->rating()
			],
			'review_count' => $reviewCount,
			'distance' => $venue->distance,
			'has_owner' => $venue->has_owner,
			'created_at' => $venue->created_at
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
	 * Include amenities.
	 *
	 * @param Venue $venue
	 * @return \League\Fractal\Resource\Collection
	 */
	public function includeAmenities(Venue $venue)
	{
		return $this->collection($venue->amenities, function(Amenity $amenity) {
			return $amenity->only('id', 'machine_name', 'country');
		});
	}

	/**
	 * Include amenity ids.
	 *
	 * @param Venue $venue
	 * @return \League\Fractal\Resource\Item
	 */
	public function includeAmenityIds(Venue $venue)
	{
		return $this->item($venue->amenities, function(Collection $amenities) {
			return $amenities->pluck('id')->all();
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
			return $category->only('id', 'machine_name', 'name', 'country');
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
			return $vltPlatform->only('id', 'name', 'country');
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
		$subscription = $venue->subscription();

		if (!$subscription) return null;

		return $this->item($subscription, function(Subscription $subscription) {
			return [
				'name' => $subscription->name,
				'currency' => $subscription->currency,
				'price' => $subscription->price,
				'distance_bonus' => $subscription->distance_bonus,
				'hide_nearby_venues' => $subscription->hide_nearby_venues,
				'home_page_highlight' => $subscription->home_page_highlight,
				'ends_at' => $subscription->ends_at,
				'updated_at' => $subscription->updated_at,
				'current_period_ends_at' => $subscription->current_period_ends_at,
				'needs_payment' => $subscription->hasIncompletePayment()
			];
		});
	}

	/**
	 * Include reviews.
	 *
	 * @param Venue $venue
	 * @return \League\Fractal\Resource\Collection
	 */
	public function includeReviews(Venue $venue)
	{
		$venue->load([
			'reviews' => function($query) {
				$query->withComment();
			},
			'reviews.user'
		]);

		return $this->collection($venue->reviews, function(Review $review) {
			return [
				'id' => $review->id,
				'title' => $review->title,
				'body' => $review->body,
				'rating' => $review->rating,
				'language' => $review->language,
				'reply' => $review->reply,
				'created_at' => $review->created_at,
				'replied_at' => $review->replied_at,
				'user' => [
					'name' => $review->user->name
				]
			];
		});
	}
}
