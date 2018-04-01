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
		return [
			'id' => $file->id,
			// 'name' => $file->name,
			// 'extension' => $file->extension,
			// 'mime_type' => $file->mime_type,
			// 'size' => $file->size,
			'caption' => $file->caption,
			// 'original_url' => $file->original_url,
			'resized_url' => $file->resized_url,
			'thumbnail_url' => $file->thumbnail_url
		];
	}
}
