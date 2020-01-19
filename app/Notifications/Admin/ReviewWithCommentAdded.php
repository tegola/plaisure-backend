<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use App\Models\Review;

class ReviewWithCommentAdded extends Notification
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(Review $review)
	{
		$this->review = $review;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return ['slack'];
	}

	/**
	 * Get the Slack representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return SlackMessage
	 */
	public function toSlack($notifiable)
	{
		return (new SlackMessage)
			->warning()
			->content('A new review has been added and might need moderation.')
			->attachment(function($attachment){
				$venue = $this->review->venue;

				$attachment
					->content($this->review->body)
					->fields([
						'Venue' => "{$venue->name} ({$venue->id})",
						'ID' => $this->review->id,
						'User' => $this->review->user->name,
						'Title' => $this->review->title
					]);
			});
	}
}
