<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\VenueCategory;

class VenueCategoryTransformer extends TransformerAbstract
{
	/**
	 * A Fractal transformer.
	 *
	 * @return array
	 */
	public function transform(VenueCategory $category)
	{
		return [
			'id' => $category->id,
			'machine_name' => $category->machine_name,
			'name' => $category->name
		];
	}
}
