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
		$locale = app()->getLocale() !== 'en' ? app()->getLocale() : null; // Only for locales different than en

		$url = route('password.reset', [
			'locale' => $locale, // https://github.com/laravel/framework/pull/25752#issuecomment-453869887
			'email' => $notifiable->email,
			'token' => $this->token
		]);

		return (new MailMessage)
			->subject(__('emails.reset_password.subject'))
			->view('mail.password.reset', compact('notifiable', 'url'));
	}
}
