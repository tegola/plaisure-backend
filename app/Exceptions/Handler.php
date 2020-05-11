<?php

namespace App\Exceptions;

use App\Notifications\Admin\ExceptionOccurred as ExceptionOccurredNotification;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Notification;
use Throwable;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		//
	];

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array
	 */
	protected $dontFlash = [
		'password',
		'password_confirmation',
	];

	/**
	 * Report or log an exception.
	 *
	 * @param  \Throwable  $exception
	 * @return void
	 */
	public function report(Throwable $exception)
	{
		// Route notifications via email
		if ($this->shouldReport($exception) && !app()->isLocal()) {
			$notification = new ExceptionOccurredNotification($exception); 
			Notification::route('mail', 'alan@qreate.it')->notify($notification);
		}

		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Throwable  $exception
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Throwable $exception)
	{
		return parent::render($request, $exception);
	}
}
