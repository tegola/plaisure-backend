<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Transformers\VenueTransformer;

class UserController extends Controller
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

		return compact('user');
	}

	/**
	 * Get the venues for the logged in user.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function venues()
	{
		$venues = auth()->user()->venues
			->each(function($venue) { // Load only first photo
				$venue->load([
					'photos' => function($query) {
						$query->take(1);
					}
				]);
			})
			->transformWith(new VenueTransformer())
			->parseIncludes([
				'categories',
				'photos',
				'subscription'
			]);

		return compact('venues');
	}

	/**
	 * Get the user data for the edit form.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function edit()
	{
		$user = auth()->user();

		return compact('user');
	}

	/**
	 * Update the logged in user data.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request)
	{
		$user = auth()->user();

		// Prepare validation rules
		// Billing rules depend on active subscriptions. At least one active
		// subscriptions means that the user cannot nullify its billing data.
		// Otherwise, billing fields are only required if any one of them is
		// present.
		$rules = [
			'name'            => 'required|string|max:255',
			'locale'          => 'required',
			'new_password'    => 'nullable|string|min:8|confirmed',
			'send_newsletter' => 'boolean'
		];

		if ($user->subscriptions()->active()->count()) {
			$rules = array_merge($rules, [
				'legal_name'       => 'required|string',
				'address_line1'    => 'required|string',
				'address_line2'    => 'nullable|string',
				'address_city'     => 'required|string',
				'address_postcode' => 'required|string',
				'address_region'   => 'required|string',
				'country'          => 'required|string',
				'vat_number'       => 'required|string|max:20',
			]);
		} else {
			$rules = array_merge($rules, [
				'legal_name'       => "nullable|{$this->prepareRequiredWithRule('legal_name')}|string",
				'address_line1'    => "nullable|{$this->prepareRequiredWithRule('address_line1')}|string",
				'address_line2'    => 'nullable|string',
				'address_city'     => "nullable|{$this->prepareRequiredWithRule('address_city')}|string",
				'address_postcode' => "nullable|{$this->prepareRequiredWithRule('address_postcode')}|string",
				'address_region'   => "nullable|{$this->prepareRequiredWithRule('address_region')}|string",
				'country'          => "nullable|{$this->prepareRequiredWithRule('country')}|string",
				'vat_number'       => "nullable|{$this->prepareRequiredWithRule('vat_number')}|string|max:20",
			]);
		}

		// Validate fields
		$request->validate($rules);

		// Save user data
		$user->fill([
			'name' => $request->name,
			'locale' => $request->locale,
			'legal_name' => $request->legal_name ?: '',
			'address_line1' => $request->address_line1 ?: '',
			'address_line2' => $request->address_line2 ?: '',
			'address_city' => $request->address_city ?: '',
			'address_postcode' => $request->address_postcode ?: '',
			'address_region' => $request->address_region ?: '',
			'country' => $request->country ?: '',
			'vat_number' => $request->vat_number ?: '',
			'send_newsletter' => $request->send_newsletter
		]);

		// Save new password (if set)
		if ($request->new_password) {
			$user->password = bcrypt($request->new_password);
		}

		// Save user and Stripe customer
		$user->save();
		$user->updateStripeCustomer();
	}

	/**
	 * Prepare the required_with validation rule for billing fields.
	 * 
	 * @param  string $except The field to exclude
	 * @return string "required_with:field1,field2,..."
	 */
	private function prepareRequiredWithRule(string $except)
	{
		$fields = [
			'legal_name',
			'address_line1',
			'address_city',
			'address_postcode',
			'address_region',
			'country',
			'vat_number'
		];

		// Remove excepted field
		$fields = array_filter($fields, function($field) use ($except) {
			return $field !== $except;
		});

		return 'required_with:'. implode(',', $fields);
	}
}