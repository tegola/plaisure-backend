<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;
use Illuminate\Auth\Access\HandlesAuthorization;

class VenuePolicy
{
	use HandlesAuthorization;

	public function before(User $user, $ability) {
		if ($user->is_admin) return true;
	}

	/**
	 * Determine whether the user can claim the venue.
	 *
	 * @param  User   $user
	 * @param  Venue  $venue
	 * @return boolean
	 */
	public function claim(User $user, Venue $venue)
	{
		return !$venue->has_owner;
	}

	/**
	 * Determine whether the user can create venues.
	 *
	 * @param  User   $user
	 * @return boolean
	 */
	public function create(User $user)
	{
		return $user->is_owner;
	}

	/**
	 * Determine whether the user can update the venue.
	 *
	 * @param  User   $user
	 * @param  Venue  $venue
	 * @return boolean
	 */
	public function update(User $user, Venue $venue)
	{
		return $user->is_owner && $user->id == $venue->owner_id;
	}

	/**
	 * Determine whether the user can add a review for the venue.
	 *
	 * @param  User   $user
	 * @param  Venue  $venue
	 * @return boolean
	 */
	public function review(User $user, Venue $venue)
	{
		// An owner cannot review its own venue
		return $user->id !== $venue->owner_id;
	}

	/**
	 * Determine whether the user can delete the venue.
	 *
	 * @param  User   $user
	 * @param  Venue  $venue
	 * @return boolean
	 */
	public function delete(User $user, Venue $venue)
	{
		return $user->is_owner && $user->id == $venue->owner_id;
	}
}
