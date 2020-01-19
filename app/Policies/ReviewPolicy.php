<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Review;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
{
	use HandlesAuthorization;

	public function before(User $user, $ability) {
		if ($user->is_admin) return true;
	}

	/**
	 * Determine whether the user can update the review.
	 * 
	 * @param  User   $user
	 * @param  Review $review
	 * @return boolean
	 */
	public function update(User $user, Review $review)
	{
		return $user->is($review->user);
	}

	/**
	 * Determine whether the user can reply to the review. Only owners can
	 * reply to reviews.
	 * 
	 * @param  User   $user
	 * @param  Review $review
	 * @return boolean
	 */
	public function reply(User $user, Review $review)
	{
		return $user->is($review->venue->owner);
	}

	/**
	 * Determine whether the user can delete the review.
	 * 
	 * @param  User   $user
	 * @param  Review $review
	 * @return boolean
	 */
	public function delete(User $user, Review $review)
	{
		return $user->is($review->user);
	}
}
