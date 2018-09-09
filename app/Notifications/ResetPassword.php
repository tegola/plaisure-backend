<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($token)
	{
		$this->token = $token;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return ['mail'];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable)
	{
		$message = new MailMessage();

		$message
			->subject('Reset your password')
			->greeting("Hello {$notifiable->name},")
			->salutation('pippo!')
			->line('You are receiving this email because we received a password reset request for your account.')
			->action('Reset password', url("/password/reset/{$notifiable->email}/{$this->token}"))
			->line('If you did not request a password reset, no further action is required.');

		return $message;
	}
}
