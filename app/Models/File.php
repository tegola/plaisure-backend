<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
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
	 * Get all of the owning filable models.
	 */
	public function filable()
	{
		return $this->morphTo();
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
	* Determine if the attachment is an image.
	*
	* @return boolean
	*/
	public function isImage() {
		$mime_array = explode('/', $this->mime_type);

		return count($mime_array) && $mime_array[0] == 'image' ? true : false;
	}
}
