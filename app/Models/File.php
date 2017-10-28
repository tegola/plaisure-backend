<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Storage;
use Image;

class File extends Model
{
	// FIXME: Move to enums: http://www.php.net/manual/en/class.splenum.php
	const UPLOAD_DIR = 'uploads';
	const PUBLIC_DIR = 'public';

	const SIZE_ORIGINAL = 'original';
	const SIZE_RESIZED = 'resized';
	const SIZE_THUMBNAIL = 'thumbnail';

	const TYPE_UNKNOWN = 0;
	const TYPE_VENUE_PHOTO = 1;
	const TYPE_USER_PHOTO = 2;

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = [
		'resized_url',
		'thumbnail_url'
	];

	/**
	 * The attributes that should be visible in arrays.
	 *
	 * @var array
	 */
	protected $visible = [
		'id',
		'name',
		'extension',
		'mime_type',
		'size',
		'caption',
		'resized_url',
		'thumbnail_url'
	];

	/**
	 * Get all of the owning filable models.
	 */
	public function filable()
	{
		return $this->morphTo();
	}

	/**
	 * Cretes a new File from a Request's uploaded file, fills it with the file
	 * data and saves it on the filesystem.
	 * 
	 * @param  UploadedFile $file
	 * @param  int $type The type from the list of known attachment types.
	 * @return self
	 */
	static function createFromUpload(UploadedFile $uploadedFile, $type = self::TYPE_UNKNOWN) {
		// Check that the type is the list of known types
		if ($type !== null) {
			if (!in_array($type, [
				self::TYPE_UNKNOWN,
				self::TYPE_VENUE_PHOTO,
				self::TYPE_USER_PHOTO
			])) {
				throw new \Exception('Unknown file type specified.');
			}
		}

		// Save the file
		$storedFilePath = $uploadedFile->store(self::UPLOAD_DIR);

		// Save the instance
		$file = self::create([
			'type' => $type,
			'token' => str_random(5),
			'path' => $storedFilePath,
			'name' => $uploadedFile->getClientOriginalName(),
			'mime_type' => $uploadedFile->getClientMimeType(),
			'size' => $uploadedFile->getClientSize()
		]);

		// Resize
		$file->resize();

		return $file;
	}

	/**
	 * Get the user that has uploaded this file.
	 * 
	 * @return App\Models\User
	 */
	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	/**
	 * Get the venue that this file was uploaded for.
	 * 
	 * @return App\Models\Venue
	 */
	public function venue()
	{
		return $this->belongsTo('App\Models\Venue');
	}

	/**
	 * Get the url for the original file.
	 * 
	 * @return string
	 */
	public function getOriginalUrlAttribute()
	{
		return $this->urlForSize(self::SIZE_ORIGINAL);
	}

	/**
	 * Get the url for the resized file.
	 * 
	 * @return string
	 */
	public function getResizedUrlAttribute()
	{
		return $this->urlForSize(self::SIZE_RESIZED);
	}

	/**
	 * Get the url for the thumbnail file.
	 * 
	 * @return string
	 */
	public function getThumbnailUrlAttribute()
	{
		return $this->urlForSize(self::SIZE_THUMBNAIL);
	}

	/**
	* Determine if the attachment is an image.
	*
	* @return boolean
	*/
	public function isImage()
	{
		$mimeArray = explode('/', $this->mime_type);

		return count($mimeArray) && $mimeArray[0] == 'image' ? true : false;
	}

	/**
	 * Build the file name for the specified image size.
	 * 
	 * @param  string $size One of the sizes specified in the constants.
	 * @return string
	 */
	public function internalFilenameForSize($size = self::SIZE_ORIGINAL)
	{
		$pathInfo = pathInfo($this->path);

		// Original size, return the normal file name
		if ($size == self::SIZE_ORIGINAL) return $pathInfo['basename'];

		// Size specified, return the modified file name
		switch ($size) {
			case self::SIZE_THUMBNAIL:
				$sizeStr = '_tn';
				break;
			case self::SIZE_RESIZED:
			default:
				$sizeStr = '_res';
				break;
		}

		return implode('', [
			$pathInfo['filename'],
			$sizeStr,
			'.',
			$pathInfo['extension']
		]);
	}

	/**
	 * Build the path for the specified image size.
	 * 
	 * @param  string $size One of the sizes specified in the constants.
	 * @return string
	 */
	public function pathForSize($size = self::SIZE_ORIGINAL)
	{
		// Original size, return the original path
		if ($size == self::SIZE_ORIGINAL) return $this->path;

		// Size specified, modify the path
		$pathInfo = pathinfo($this->path);
		$sizePath = implode('', [
			$pathInfo['dirname'],
			'/',
			$this->internalFilenameForSize($size)
		]);

		return $sizePath;
	}

	/**
	 * Build the path starting from root for the specified image size.
	 * 
	 * @param  string $size One of the sizes specified in the constants.
	 * @return string
	 */
	public function rootPathForSize($size = self::SIZE_ORIGINAL)
	{
		$path = $this->pathForSize($size);

		return storage_path("app/{$path}");
	}

	/**
	 * Resizes the file ('resized' and 'thumbnail'), but only if it's an image.
	 * 
	 * @return boolean
	 */
	public function resize()
	{
		// Stop if it's not an image
		if (!$this->isImage()) return false;

		$originalPath = $this->rootPathForSize(self::SIZE_ORIGINAL);
		$resizedPath = $this->rootPathForSize(self::SIZE_RESIZED);
		$thumbnailPath = $this->rootPathForSize(self::SIZE_THUMBNAIL);

		// Resized version
		Image::make($originalPath)
			->resize(1920, 1920, function ($constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			})
			->save($resizedPath);

		// Thumbnail version
		Image::make($originalPath)
			->resize(640, 640, function ($constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			})
			->save($thumbnailPath);

		return true;
	}

	/**
	 * Check whether the file is public (=it's in the public dir) or not.
	 * 
	 * @return boolean
	 */
	public function isPublic()
	{
		$pathArray = explode('/', $this->path);

		return count($pathArray) && $pathArray[0] == self::PUBLIC_DIR ? true : false;
	}

	/**
	 * Moves the file in the public directory and updates the model.
	 * 
	 * @return bool
	 */
	public function makePublic()
	{
		// Move files
		$publicDir = self::PUBLIC_DIR;
		$originalPaths = [
			self::SIZE_ORIGINAL => $this->pathForSize(self::SIZE_ORIGINAL),
			self::SIZE_RESIZED => $this->pathForSize(self::SIZE_RESIZED),
			self::SIZE_THUMBNAIL => $this->pathForSize(self::SIZE_THUMBNAIL)
		];

		foreach($originalPaths as $size => $path) {
			$oldPath = $path;
			$fileName = basename($oldPath);
			$newPath = "{$publicDir}/{$fileName}";
			if ($size == self::SIZE_ORIGINAL) $originalNewPath = $newPath;

			Storage::move($oldPath, $newPath);
		}

		// Update model
		return $this->update([
			'path' => $originalNewPath
		]);
	}

	/**
	 * Build the public url for the specified image size.
	 * 
	 * @param  string $size One of the sizes specified in the constants.
	 * @return string
	 */
	public function urlForSize($size = self::SIZE_ORIGINAL) {
		$filename = $this->internalFilenameForSize($size);

		if ($this->isPublic()) {
			return asset("/storage/{$filename}");
		} else {
			return route('files.show', [
				'file' => $this,
				'size' => $size,
				'token' => $this->token
			]);
		}
		return ;
	}

	/**
	 * Delete the model from the database and the file(s) on the filesystem.
	 *
	 * @return bool|null
	 * @throws \Exception
	 */
	public function delete()
	{
		// Delete file and its variations
		Storage::delete([
			$this->pathForSize(self::SIZE_ORIGINAL),
			$this->pathForSize(self::SIZE_RESIZED),
			$this->pathForSize(self::SIZE_THUMBNAIL)
		]);

		// Delete record
		return parent::delete();
	}

	/**
	 * Files that aren't attached to any element.
	 * 
	 * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
	 * @return Illuminate\Database\Query\Builder          Modified query builder
	 */
	public function scopeOrphans($query)
	{
		return $query->where('filable_id', 0);
	}
}
