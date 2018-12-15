<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use App\Models\User;

class UserRegistered extends Notification
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
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
		$user = $this->user;

		return (new SlackMessage)
			->content('A new user has just registered')
			->attachment(function($attachment) use ($user) {
				$attachment
					->title($user->name, config('app.url'))
					->fields([
						'Name' => $user->name,
						'E-mail' => $user->email
					]);
			});
	}
}
