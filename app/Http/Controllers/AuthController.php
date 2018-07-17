<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\User;

class AuthController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	    $this
	    	->middleware('auth:api')
	    	->except('register', 'login', 'refresh');
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
		]);

		// TODO: Send email confirmation

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
	 * Refresh tokens using the old refresh token.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
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

	/**
	 * Revoke access token, i.e.: logout.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function logout(Request $request) {
		$request->user()->token()->revoke();

		return response(null, 200);
	}
}