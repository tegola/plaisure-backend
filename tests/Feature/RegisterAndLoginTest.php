<?php

namespace Tests\Feature;

use Arr;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterAndLoginTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * The url for registering.
	 *
	 * @var string
	 */
	private $registerUrl = '/api/auth/register';

	/**
	 * The url for logging in.
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
	/*
	public function testUserRegistration()
	{
		$this->runRegisterAndLoginTest();
	}
	*/

	/**
	 * Test correct owner registration.
	 *
	 * @return void
	 */
	public function testOwnerRegistration()
	{
		$this->runRegisterAndLoginTest(true);
	}

	/**
	 * Test all required fields.
	 *
	 * @return void
	 */
	public function testRequiredFields()
	{
		$this
			->postJson($this->registerUrl)
			->assertJsonValidationErrors([
				'locale',
				'name',
				'email',
				'password'
			]);
	}

	public function testPasswordLength()
	{
		$this
			->postJson($this->registerUrl, ['password' => '123456'])
			->assertJsonValidationErrors(['password']);
	}

	/**
	 * Run test for registering and login.
	 *
	 * @param  boolean $asOwner Whether to register as a owner or not
	 * @return void
	 */
	private function runRegisterAndLoginTest($asOwner = false)
	{
		$userData = [
			'locale' => 'en-GB',
			'name' => $asOwner ? 'Owner user' : 'Test user',
			'email' => 'owner@email.com',
			'password' => '12345678'
		];

		if ($asOwner) {
			$userData['is_owner'] = true;
		}

		// Register
		$this
			->postJson($this->registerUrl, $userData)
			->assertSuccessful();

		// Login
		$loginRequest = $this
			->postJson($this->loginUrl, Arr::only($userData, ['email', 'password']))
			->assertSuccessful()
			->assertJsonStructure(['access_token']);

		// Get access token
		$token = $loginRequest->json('access_token');

		// Get user
		$this
			->getJson($this->userUrl, ['Authorization' => "Bearer {$token}"])
			->assertSuccessful()
			->assertJson([
				'user' => Arr::except($userData, ['password'])
			]);
	}
}
