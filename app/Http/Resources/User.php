<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
			'id' => $this->id,
			'name' => $this->name,
			'email' => $this->email,
			'locale' => $this->locale,
			'legal_name' => $this->legal_name,
			'address_line1' => $this->address_line1,
			'address_line2' => $this->address_line2,
			'address_city' => $this->address_city,
			'address_postcode' => $this->address_postcode,
			'address_region' => $this->address_region,
			'country' => $this->country,
			'vat_number' => $this->vat_number,
			'send_newsletter' => $this->send_newsletter,
			'is_owner' => $this->is_owner
		];
	}
}
