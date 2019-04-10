<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BillingUpcoming extends Notification
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($invoice, $subscription)
	{
		$this->invoice = $invoice;
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
		$venue = $this->subscription->venue;
		$invoice = $this->invoice;

		return (new MailMessage)
			->subject(__('emails.billing_upcoming.subject'))
			->view('mail.billing.upcoming', compact('notifiable', 'invoice', 'venue'));
	}
}
