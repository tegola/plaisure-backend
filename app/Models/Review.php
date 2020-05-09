<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
	use SoftDeletes;

	/**
	 * The model's default attributes.
	 *
	 * @var array
	 */
	protected $attributes = [
		'title' => '',
		'body' => '',
		'reply' => ''
	];

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];	

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'replied_at' => 'datetime'
	];

	/**
	 * User that created this review.
	 * 
	 * @return \App\Models\User
	 */
	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	/**
	 * Venue this review is for.
	 * 
	 * @return \App\Models\Venue
	 */
	public function venue()
	{
		return $this->belongsTo('App\Models\Venue');
	}

	/**
	 * Reviews that have only rating (no comment).
	 * 
	 * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
	 * @return Illuminate\Database\Query\Builder          Modified query builder
	 */
	public function scopeWithoutComment($query)
	{
		return $query
			->where('title', '')
			->where('body', '');
	}

	/**
	 * Reviews that have comments (not just rating).
	 * 
	 * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
	 * @return Illuminate\Database\Query\Builder          Modified query builder
	 */
	public function scopeWithComment($query)
	{
		return $query
			->where('title', '!=', '')
			->orWhere('body', '!=', '');
	}
}