<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AuthController extends Controller
{
	/**
	 * Login with email/password and receive access and refresh tokens.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function login(Request $request) {
		// Validate fields
		$request->validate([
			'email' => 'required|email',
			'password' => 'required|string'
		]);

		// Login using password grant client
		$client = new Client();
		$response = $client->post(url('/oauth/token'), [
			'form_params' => [
				'grant_type' => 'password',
				'client_id' => env('APP_CLIENT_ID'),
				'client_secret' => env('APP_CLIENT_SECRET'),
				'username' => $request->email,
				'password' => $request->password,
				'scope' => ''
			],
			'http_errors' => false // Automatically handle errors
		]);

		return json_decode($response->getBody(), true);
	}

	/**
	 * Refresh access and refresh tokens using the old refresh token.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Request
	 */
	public function refresh(Request $request) {
		// Refresh token using password grant client
		$client = new Client();
		$response = $client->post(url('/oauth/token'), [
			'form_params' => [
				'grant_type' => 'refresh_token',
				'client_id' => env('APP_CLIENT_ID'),
				'client_secret' => env('APP_CLIENT_SECRET'),
				'refresh_token' => $request->refresh_token,
				'scope' => ''
			],
			'http_errors' => false // Automatically handle errors
		]);

		return json_decode($response->getBody(), true);
	}

	public function register(Request $request) {

	}
}