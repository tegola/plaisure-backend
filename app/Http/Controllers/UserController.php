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
		$user = $request->user();
		$venues = $user
			->venues()
			->with([
				'photos' => function($query) {
					$query->first();
				}
			])
			->get()
			->transformWith(new VenueTransformer())
			->includeCategories()
			->includePhotos()
			->includePlan();

		return [
			'user' => $user,
			'venues' => $venues
		];
	}

	/**
	 * Update the logged in user data.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request)
	{
		$user = $request->user();
		$formValues = $request->all();

		// Validate fields
		$request->validate([
			'name'                         => 'required|string|max:255',
			'legal_name'                   =>  $this->requiredLegalFieldsExcept('legal_name') .'|string',
			'address_street'               =>  $this->requiredLegalFieldsExcept('address_street') .'|string',
			'address_city'                 =>  $this->requiredLegalFieldsExcept('address_city') .'|string',
			'address_postcode'             =>  $this->requiredLegalFieldsExcept('address_postcode') .'|string',
			'address_region'               =>  $this->requiredLegalFieldsExcept('address_region') .'|string',
			'address_country'              =>  $this->requiredLegalFieldsExcept('address_country') .'|string',
			'vat_number'                   =>  $this->requiredLegalFieldsExcept('vat_number') .'|string|max:20',
			'new_password'                 => 'nullable|string|min:8|confirmed',
			'send_newsletter'              => 'boolean'
		]);

		// Save user data
		$user->name = $request->input('name');
		$user->legal_name = $request->input('legal_name');
		$user->address_street = $request->input('address_street');
		$user->address_city = $request->input('address_city');
		$user->address_postcode = $request->input('address_postcode');
		$user->address_region = $request->input('address_region');
		$user->address_country = $request->input('address_country');
		$user->vat_number = $request->input('vat_number');
		$user->send_newsletter = $request->input('send_newsletter');

		// Save new password
		if ($request->has('new_password')) {
			$user->password = bcrypt($request->input('new_password'));
		}

		// Save user
		$user->save();

		// Return user data
		return [
			'user' => $request->user()
		];
	}

	/**
	 * Prepare the legal fields required_with validation rule.
	 * 
	 * @param  string $name The field to exclude
	 * @return string "required_with:field1,field2,..."
	 */
	private function requiredLegalFieldsExcept($name) {
		$fields = [
			'legal_name',
			'address_street',
			'address_city',
			'address_postcode',
			'address_region',
			'address_country',
			'vat_number'
		];

		$filtered = array_filter($fields, function($field) use ($name) {
			return $field != $name;
		});

		return 'required_with:'. implode(',', $filtered);
	}
}