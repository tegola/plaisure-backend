<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * The url for testing user registration.
	 *
	 * @var string
	 */
	private $registerUrl = '/api/auth/register';

	private $userData = [
		'locale' => 'en-GB',
		'name' => 'Test user',
		'email' => 'alan@qreate.it',
		'password' => '12345678'
	];

	/**
	 * Test correct user registration.
	 *
	 * @return void
	 */
	public function testUserRegistration()
	{
		$response = $this->post($this->registerUrl, $this->userData);

		$response->assertSuccessful();
	}

	/**
	 * Test correct owner registration.
	 *
	 * @return void
	 */
	public function testOwnerRegistration()
	{
		$ownerData = ['is_owner' => true];
		$response = $this->post($this->registerUrl, array_merge($this->userData, $ownerData));

		$response->assertJson($ownerData);
	}

	/**
	 * Test all required fields.
	 *
	 * @return void
	 */
	public function testRequiredFields()
	{
		$response = $this->post($this->registerUrl);

		$response->assertSessionHasErrors([
			'locale',
			'name',
			'email',
			'password'
		]);
	}
}
