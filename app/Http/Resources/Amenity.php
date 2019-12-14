<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Amenity extends JsonResource
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
			'machine_name' => $this->machine_name,
			'country' => $this->country // To limit them client/side depending on the language
		];
	}
}
