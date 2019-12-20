<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;
use VenueCategoriesTableSeeder;

class VenueCreationTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp(): void
	{
		parent::setUp();

		// Seed categories
		$this->seed(VenueCategoriesTableSeeder::class);

		// Create user
		$user = User::create([
			'locale' => 'en-GB',
			'name' => 'Owner user',
			'email' => 'owner@email.com',
			'is_owner' => true
		]);

		Passport::actingAs($user);
	}

	/**
	 * Test venue creation.
	 *
	 * @group venue-creation
	 * @return void
	 */
	public function testAddVenue()
	{
		$this
			->postJson('/api/venues', [
				'name' => 'Test venue',
				'address' => [
					'line1' => 'line 1',
					'city' => 'City',
					'postcode' => '12345',
					'province' => 'PR'
				],
				'country' => 'GB',
				'coords' => [
					'lat' => 42,
					'lng' => 12
				],
				'category_ids' => [1]
			])
			->assertSuccessful();
	}
}
