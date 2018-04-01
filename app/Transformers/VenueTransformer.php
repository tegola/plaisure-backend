<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Venue;
use App\Transformers\VenueCategoryTransformer;
use App\Transformers\FileTransformer;

class VenueTransformer extends TransformerAbstract
{
	/**
	 * List of resources possible to include
	 *
	 * @var array
	 */
	protected $availableIncludes = [
		'categories',
		'photos'
	];

	/**
	 * List of resources to automatically include
	 *
	 * @var array
	 */
	protected $defaultIncludes = [
		// 'categories'
	];
	
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
				'street' => $venue->address_street,
				'number' => $venue->address_number,
				'city' => $venue->address_city,
				'postcode' => $venue->address_postcode,
				'province' => $venue->address_province,
				'region' => $venue->address_region,
				'country' => $venue->address_country,
				'short' => $venue->short_address,
				'long' => $venue->long_address
			],
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
				'tripadvisor' => $venue->url_tripadvisor,
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
			'distance' => $venue->distance
			// 'categories' => [] // Included
		];
	}

	/**
	 * Include categories.
	 *
	 * @param Venue $venue
	 * @return \League\Fractal\Resource\Collection
	 */
	public function includeCategories(Venue $venue)
	{
		$categories = $venue->categories;

		return $this->collection($categories, new VenueCategoryTransformer());
	}

	/**
	 * Include photos.
	 *
	 * @param Venue $venue
	 * @return \League\Fractal\Resource\Collection
	 */
	public function includePhotos(Venue $venue)
	{
		$photos = $venue->photos;

		return $this->collection($photos, new FileTransformer());
	}
}
