<?php

namespace App\Http\Controllers\Site\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Venue as VenueResource;

class MainController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth:api');
	}

	/**
	 * Get the logged in user data.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function user(Request $request)
	{
		$user = auth()->user();
		$user->venue_ids = $user->venues()->pluck('id_hashed')->all();
		$user->favorite_ids = $user->favorites()->pluck('id_hashed')->all();

		return compact('user');
	}

	/**
	 * Get the user data for the edit form.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function edit()
	{
		return new UserResource(auth()->user());
	}

	/**
	 * Update the logged in user's personal information.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function info(Request $request)
	{
		$user = auth()->user();

		// Validate fields
		$request->validate([
			'name'            => 'required|string|max:255',
			'locale'          => 'required',
			'send_newsletter' => 'boolean'
		]);

		// Fill data
		$user->fill([
			'name' => $request->name,
			'locale' => $request->locale,
			'send_newsletter' => $request->send_newsletter
		]);

		// Save user and Stripe customer
		$user->save();
		$user->updateStripeCustomer();
	}

	/**
	 * Update the logged in user's billing information.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function billing(Request $request)
	{
		$user = auth()->user();

		// Validate fields
		$request->validate([
			'legal_name'       => 'required|string',
			'address_line1'    => 'required|string',
			'address_line2'    => 'nullable|string',
			'address_city'     => 'required|string',
			'address_postcode' => 'required|string',
			'address_region'   => 'required|string',
			'country'          => 'required|string',
			'vat_number'       => 'required|string|max:20',
		]);

		// Fill data
		$user->fill([
			'legal_name' => $request->legal_name,
			'address_line1' => $request->address_line1,
			'address_line2' => $request->address_line2 ?: '',
			'address_city' => $request->address_city,
			'address_postcode' => $request->address_postcode,
			'address_region' => $request->address_region,
			'country' => $request->country,
			'vat_number' => $request->vat_number
		]);

		// Save user and Stripe customer
		$user->save();
		$user->updateStripeCustomer();
	}

	/**
	 * Change the logged in user's password.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function password(Request $request)
	{
		$user = auth()->user();

		// Validate fields
		$request->validate([
			'new_password' => 'nullable|string|min:8|confirmed'
		]);

		// Change password and save user
		$user->password = bcrypt($request->new_password);
		$user->save();
	}
}