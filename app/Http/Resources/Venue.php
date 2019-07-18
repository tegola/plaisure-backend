<?php

namespace App\Http\Resources;

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
		return [
			'id' => $this->id_hashed,
			// 'owner_id' => $this->owner_id,
			'concessionaire_id' => $this->concessionaire_id,
			// 'aams_census_code' => '',
			// 'aams_subject_enrollment_code' => '',

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
			'machine_type' => $this->machine_type,
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
			'amenities' => [
				'atm' => $this->amenity_atm,
				'bar' => $this->amenity_bar,
				'pay_per_view' => $this->amenity_pay_per_view,
				'pos' => $this->amenity_pos,
				'private_parking' => $this->amenity_private_parking,
				'restaurant' => $this->amenity_restaurant,
				'security' => $this->amenity_security,
				'smoking_area' => $this->amenity_smoking_area,
				'wifi' => $this->amenity_wifi
			],
			'distance' => $this->distance,
			'has_owner' => $this->has_owner,
			'created_at' => (string) $this->created_at
		];
	}
}
