<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon;

class BillingUpcoming extends Notification
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($subscription)
	{
		$this->subscription = $subscription;
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
		$routeLocale = app()->getLocale() !== 'en' // Only for locales different than "en"
			? app()->getLocale()
			: null;

		return (new MailMessage)
			->subject(__('emails.billing_upcoming.subject'))
			->view('mail.billing.upcoming', [
				'notifiable' => $notifiable,
				'subscription' => $this->subscription,
				'venue' => $this->subscription->venue,
				'loginUrl' => route('login', ['locale' => $routeLocale]), // https://github.com/laravel/framework/pull/25752#issuecomment-453869887
				'supportEmail' => env('MAIL_SUPPORT_ADDRESS')
			]);
	}
}
