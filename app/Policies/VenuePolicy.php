<?php

namespace App\Policies;

use App\Models\User;
use App\Venue;
use Illuminate\Auth\Access\HandlesAuthorization;

class VenuePolicy
{
	use HandlesAuthorization;

	public function before(User $user, $ability) {
		// In dev env an admin can do everything
		if (app()->isLocal() && $user->is_admin) return true;

		// In production evn an admin can't claim an activity
		if ($user->is_admin && $ability != 'claim') return true;
	}

	/**
	 * Determine whether the user can view the venue.
	 *
	 * @param  \App\Models\User  $user
	 * @param  \App\Venue  $venue
	 * @return mixed
	 */
	public function view(User $user, Venue $venue)
	{
		//
	}

	/**
	 * Determine whether the user can claim the venue.
	 *
	 * @param  \App\Models\User  $user
	 * @param  \App\Venue  $venue
	 * @return mixed
	 */
	public function claim(User $user, Venue $venue)
	{
		// Avoid letting admins claim venues to their users
		if ($user->is_admin) return false;

		return !$venue->has_owner;
	}

	/**
	 * Determine whether the user can create venues.
	 *
	 * @param  \App\Models\User  $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		return $user->is_owner;
	}

	/**
	 * Determine whether the user can update the venue.
	 *
	 * @param  \App\Models\User  $user
	 * @param  \App\Venue  $venue
	 * @return mixed
	 */
	public function update(User $user, Venue $venue)
	{
		return $user->is_owner && $user->id == $venue->owner_id;
	}

	/**
	 * Determine whether the user can delete the venue.
	 *
	 * @param  \App\Models\User  $user
	 * @param  \App\Venue  $venue
	 * @return mixed
	 */
	public function delete(User $user, Venue $venue)
	{
		return $user->is_owner && $user->id == $venue->owner_id;
	}
}
