<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\File;

class FileTransformer extends TransformerAbstract
{
	/**
	 * A Fractal transformer.
	 *
	 * @return array
	 */
	public function transform(File $file)
	{
		return $file->only([
			'id',
			'caption',
			'resized_url',
			'thumbnail_url'
		]);
	}
}
