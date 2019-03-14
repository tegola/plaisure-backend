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
		$url = route('password.reset', [
			'email' => $notifiable->email,
			'token' => $this->token
		]);

		return (new MailMessage)
			->subject(__('emails.reset_password.subject'))
			->greeting(__('emails.common.greeting_name', ['name' => $notifiable->name]))
			->line(__('emails.reset_password.intro'))
			->action(__('emails.reset_password.action'), $url)
			->line(__('emails.reset_password.outro'));
	}
}
