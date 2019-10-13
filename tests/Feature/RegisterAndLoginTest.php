<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DB;

class RegisterAndLoginTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * The url for testing user registration.
	 *
	 * @var string
	 */
	private $registerUrl = '/api/auth/register';

	/**
	 * The url for logging in
	 *
	 * @var string
	 */
	private $loginUrl = '/api/auth/login';

	/**
	 * The url for loading user data.
	 *
	 * @var string
	 */
	private $userUrl = '/api/user';

	/**
	 * Basic user data.
	 *
	 * @var array
	 */
	private $userData = [
		'locale' => 'en-GB',
		'name' => 'Test user',
		'email' => 'alan@qreate.it',
		'password' => '12345678'
	];

	protected function setUp(): void
	{
		parent::setUp();

		// Create oauth client needed for logging in
		DB::insert('insert into oauth_clients (name, secret, redirect, personal_access_client, password_client, revoked) values (?, ?, ?, ?, ?, ?)', [
			'Testing password client',
			env('OAUTH_CLIENT_SECRET'),
			config('app.url'),
			false,
			true,
			false
		]);
	}

	/**
	 * Test correct user registration.
	 *
	 * @return void
	 */
	public function testUserRegistration()
	{
		// Register
		$this
			->postJson($this->registerUrl, $this->userData)
			->assertSuccessful();

		// Login
		$loginRequest = $this->postJson($this->loginUrl, array_only($this->userData, ['email', 'password']));
		$loginRequest
			->assertSuccessful()
			->assertJsonStructure(['access_token']);

		// Get access token
		$token = $loginRequest->json('access_token');

		// Get user
		$this
			->getJson($this->userUrl, ['Authorization' => "Bearer {$token}"])
			->assertSuccessful();
	}

	/**
	 * Test correct owner registration.
	 *
	 * @return void
	 */
	/*
	public function testOwnerRegistration()
	{
		$ownerData = ['is_owner' => true];

		// Register
		$this
			->postJson($this->registerUrl, array_merge($this->userData, $ownerData))
			->assertSuccessful();

		// Login
		$loginRequest = $this->postJson($this->loginUrl, array_only($this->userData, ['email', 'password']));
		$loginRequest
			->assertSuccessful()
			->assertJsonStructure(['access_token']);

		// Get access token
		$token = $loginRequest->json('access_token');

		// Get user
		$this
			->getJson($this->userUrl, ['Authorization' => "Bearer {$token}"])
			->assertSuccessful()
			->assertJson([
				'user' => $ownerData
			]);
	}
	*/

	/**
	 * Test all required fields.
	 *
	 * @return void
	 */
	public function testRequiredFields()
	{
		$response = $this->postJson($this->registerUrl);

		$response->assertJsonValidationErrors([
			'locale',
			'name',
			'email',
			'password'
		]);
	}
}
