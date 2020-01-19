<?php

namespace App\Notifications\Admin;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;

class ExceptionOccurred extends Notification
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 *
	 * @param Exception $exception [description]
	 * @return void
	 */
	public function __construct(Exception $exception)
	{
		$this->exception = $exception;
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
		$exception = FlattenException::create($this->exception);
        $handler = new SymfonyExceptionHandler();
        $html = $handler->getHtml($exception);

		return (new MailMessage)
			->subject("Exception: {$exception->getMessage()}")
			->view('mail.admin.exception', compact('html'));
	}
}
