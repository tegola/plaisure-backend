<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Models\Review;
use App\Models\Venue;
use App\Policies\ReviewPolicy;
use App\Policies\VenuePolicy;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		Review::class => ReviewPolicy::class,
		Venue::class => VenuePolicy::class
	];

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerPolicies();

		Passport::routes();

		// Passport::tokensExpireIn(now()->addSeconds(3));

		Gate::define('administer', function($user) {
			return $user->is_admin;
		});
	}
}
