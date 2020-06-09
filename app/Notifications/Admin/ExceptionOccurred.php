<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Throwable;

class ExceptionOccurred extends Notification
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 *
	 * @param Throwable $exception
	 * @return void
	 */
	public function __construct(Throwable $exception)
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
		$handler = new HtmlErrorRenderer(true);
		$css = $handler->getStylesheet();
		$content = $handler->getBody($exception);

		return (new MailMessage)
			->subject("Exception: {$exception->getMessage()}")
			->view('mail.admin.exception', compact('css', 'content'));
	}
}
