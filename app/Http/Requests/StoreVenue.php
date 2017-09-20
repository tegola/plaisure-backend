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
		return [
			'aams_census_code'             => 'required|string',
			'aams_subject_enrollment_code' => 'required|string',
			'name'                         => 'required|string',
			'description'                  => 'nullable|string',
			'surface_size'                 => 'nullable|numeric|min:0',
			'machine_count'                => 'nullable|numeric|min:0',
			// 'machine_type'                 => ''
			'address_street'               => 'required|string',
			'address_number'               => 'required|string',
			'address_city'                 => 'required|string',
			'address_postcode'             => 'required|string',
			'address_province'             => 'required|string',
			'address_region'               => 'required|string',
			'address_country'              => 'required|string',
			'geo_latitude'                 => 'required|numeric|between:-90,90',
			'geo_longitude'                => 'required|numeric|between:-180,180',
			'categories'                   => 'required|exists:venue_categories,id',
			'contact_phone'                => 'nullable|numeric|min:0',
			'contact_email'                => 'nullable|email',
			// 'contact_facebook'             => '',
			// 'contact_twitter'              => '',
			'url_site'                     => 'nullable|url',
			'url_facebook'                 => 'nullable|url',
			'url_tripadvisor'              => 'nullable|url'
		];
	}
}
