<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\Admin\UserRegistered;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
	public function __construct()
	{
		$this->client = new Client([
			// Allow it to work even in testing environments, where we don't
			// have ssl certificates
			'verify' => false,

			// Automatically handle errors
			'http_errors' => false
		]);

		$this->middleware('guest')->except('logout');
	}

	/**
	 * Register a new user.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function register(Request $request) {
		// Validate fields
		$request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
			'password' => 'required|string|min:8'
		]);

		// Register user
		$user = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password),
			'is_owner' => true
		]);

		// Send internal Slack notification
		$notification = new UserRegistered($user);
		Notification::route('slack', env('SLACK_ACTIVITY_WEBHOOK_URL'))->notify($notification);

		// TODO: Send email confirmation

		// Login using password grant client
		/*
		$response = $this->client->post(url('/oauth/token'), [
			'form_params' => [
				'grant_type' => 'password',
				'client_id' => env('OAUTH_CLIENT_ID'),
				'client_secret' => env('OAUTH_CLIENT_SECRET'),
				'username' => $request->email,
				'password' => $request->password,
				'scope' => ''
			]
		]);

		return json_decode($response->getBody(), true);
		*/
	}

	/**
	 * Login with email/password and receive access and refresh tokens.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function login(Request $request) {
		// Validate fields
		$request->validate([
			'email' => 'required|string|email',
			'password' => 'required|string'
		]);

		// Login using password grant client
		$response = $this->client->post(url('/oauth/token'), [
			'form_params' => [
				'grant_type' => 'password',
				'client_id' => env('OAUTH_CLIENT_ID'),
				'client_secret' => env('OAUTH_CLIENT_SECRET'),
				'username' => $request->email,
				'password' => $request->password,
				'scope' => ''
			]
		]);

		return $response;
	}

	/**
	 * Refresh tokens using the old refresh token.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	/*
	public function refresh(Request $request) {
		// Refresh token using password grant client
		$response = $this->client->post(url('/oauth/token'), [
			'form_params' => [
				'grant_type' => 'refresh_token',
				'client_id' => env('OAUTH_CLIENT_ID'),
				'client_secret' => env('OAUTH_CLIENT_SECRET'),
				'refresh_token' => $request->refresh_token,
			]
		]);

		return json_decode($response->getBody(), true);
	}
	*/

	/**
	 * Log the user out of the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function logout(Request $request) {
		$user = $request->user();

		if ($user) $user->token()->revoke();

	    return response(null, 200);
	}
}