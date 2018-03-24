<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Venue;

class StoreVenue extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$venue = $this->route('venue');
		$user = $this->user();

		// Venue does not exist, it's an add
		if (!$venue) {
			return $user->is_admin;
		} else {
			$owner = $venue->user;

			if ($owner && $owner->is($user)) return true;
			if ($user->is_admin) return true;
		}

		return false;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		// Set default values for possible null values
		$this->merge([
			'description'             => $this->filled('description') ? $this->input('description') : '',
			'sports_betting'          => $this->input('sports_betting', 0),
			'virtual_betting'         => $this->input('virtual_betting', 0),
			'horse_betting'           => $this->input('horse_betting', 0),
			'arcade_roulette'         => $this->input('arcade_roulette', 0),
			'contact_phone'           => $this->filled('contact_phone') ? $this->input('contact_phone') : '',
			'contact_email'           => $this->filled('contact_email') ? $this->input('contact_email') : '',
			'contact_facebook'        => $this->filled('contact_facebook') ? $this->input('contact_facebook') : '',
			'contact_twitter'         => $this->filled('contact_twitter') ? $this->input('contact_twitter') : '',
			'url_site'                => $this->filled('url_site') ? $this->input('url_site') : '',
			'url_online_casino'       => $this->filled('url_online_casino') ? $this->input('url_online_casino') : '',
			'url_facebook'            => $this->filled('url_facebook') ? $this->input('url_facebook') : '',
			'url_tripadvisor'         => $this->filled('url_tripadvisor') ? $this->input('url_tripadvisor') : '',
			'amenity_atm'             => $this->input('amenity_atm', 0),
			'amenity_bar'             => $this->input('amenity_bar', 0),
			'amenity_pay_per_view'    => $this->input('amenity_pay_per_view', 0),
			'amenity_pos'             => $this->input('amenity_pos', 0),
			'amenity_private_parking' => $this->input('amenity_private_parking', 0),
			'amenity_restaurant'      => $this->input('amenity_restaurant', 0),
			'amenity_security'        => $this->input('amenity_security', 0),
			'amenity_smoking_area'    => $this->input('amenity_smoking_area', 0),
			'amenity_wifi'            => $this->input('amenity_wifi', 0)
		]);

		return [
			'concessionaire_id'            => 'nullable|exists:concessionaires,id',
			'aams_census_code'             => 'required|string',
			'aams_subject_enrollment_code' => 'required|string',
			'name'                         => 'required|string',
			'description'                  => 'nullable|string',
			'surface_size'                 => 'nullable|numeric|min:0',
			'vlt_machine_count'            => 'nullable|numeric|min:0',
			'awp_machine_count'            => 'nullable|numeric|min:0',
			'seating_capacity'             => 'nullable|numeric|min:0',
			'parking_capacity'             => 'nullable|numeric|min:0',
			'sports_betting'               => 'boolean',
			'virtual_betting'              => 'boolean',
			'horse_betting'                => 'boolean',
			'arcade_roulette'              => 'boolean',
			'machine_type'                 => 'nullable|numeric',
			'address_street'               => 'required|string',
			'address_number'               => 'nullable|string',
			'address_city'                 => 'required|string',
			'address_postcode'             => 'required|string',
			'address_province'             => 'required|string',
			'address_region'               => 'required|string',
			'address_country'              => 'required|string',
			'geo_latitude'                 => 'required|numeric|between:-90,90',
			'geo_longitude'                => 'required|numeric|between:-180,180',
			'contact_phone'                => 'nullable|string',
			'contact_email'                => 'nullable|email',
			'contact_facebook'             => 'nullable|string',
			'contact_twitter'              => 'nullable|string',
			'url_site'                     => 'nullable|url',
			'url_online_casino'            => 'nullable|url',
			'url_facebook'                 => 'nullable|url',
			'url_tripadvisor'              => 'nullable|url',
			'jackpot1_label'               => 'nullable|string',
			'jackpot1_value'               => 'nullable|numeric|min:0',
			'jackpot2_label'               => 'nullable|string',
			'jackpot2_value'               => 'nullable|numeric|min:0',
			'jackpot3_label'               => 'nullable|string',
			'jackpot3_value'               => 'nullable|numeric|min:0',
			'amenity_atm'                  => 'boolean',
			'amenity_bar'                  => 'boolean',
			'amenity_pay_per_view'         => 'boolean',
			'amenity_pos'                  => 'boolean',
			'amenity_private_parking'      => 'boolean',
			'amenity_restaurant'           => 'boolean',
			'amenity_security'             => 'boolean',
			'amenity_smoking_area'         => 'boolean',
			'amenity_wifi'                 => 'boolean',
			'categories'                   => 'required|exists:venue_categories,id',
			'vlt_platforms'                => 'nullable|exists:vlt_platforms,id',
			'pay_per_view_platforms'       => 'nullable|exists:pay_per_view_platforms,id',

			'business_hours'               => 'nullable|sometimes|array',
			'business_hours.*.day'         => 'required|numeric|between:0,6',
			'business_hours.*.opens'       => ['required', 'regex:/^\d\d:\d\d$/'], // FIXME: Use a time pattern (up to 24:00)
			'business_hours.*.closes'      => ['required', 'regex:/^\d\d:\d\d$/'] // FIXME: Use a time pattern (up to 24:00)
		];
	}
}
