<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
	use HasApiTokens, Notifiable;

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'send_newsletter' => 'boolean',
		'is_admin' => 'boolean',
		'is_owner' => 'boolean'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token'
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'email',
		'password'
	];

	/**
	 * Venues claimed by this user.
	 * 
	 * @return [\App\Models\Venue]
	 */
	public function venues()
	{
		return $this->hasMany('App\Models\Venue', 'owner_id');
	}

	/**
	 * Send the password reset notification.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
		$this->notify(new ResetPasswordNotification($token));
	}
}
