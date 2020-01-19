<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VenueCategory extends JsonResource
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
			'country' => $this->country,

			// Only when loaded with venue
			'is_primary' => $this->whenPivotLoaded('venue_venue_category', function() {
				// As bool to avoid creating a pivot model just for casting
				return (bool) $this->pivot->is_primary;
			})
		];
	}
}
