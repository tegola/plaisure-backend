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
		$venues = auth()->user()
			->venues()
			->with(['photos' => function($query) {
				$query->first();
			}])
			->get()
			->transformWith(new VenueTransformer())
			->includeCategories()
			->includePhotos()
			->includePlan();

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

		// Validate fields
		$request->validate([
			'name'             => 'required|string|max:255',
			'locale'           => 'required',
			'legal_name'       => 'nullable|'.$this->requiredLegalFieldsExcept('legal_name') .'|string',
			'address_line1'    => 'nullable|'.$this->requiredLegalFieldsExcept('address_line1') .'|string',
			'address_line2'    => 'nullable|string',
			'address_city'     => 'nullable|'.$this->requiredLegalFieldsExcept('address_city') .'|string',
			'address_postcode' => 'nullable|'.$this->requiredLegalFieldsExcept('address_postcode') .'|string',
			'address_region'   => 'nullable|'.$this->requiredLegalFieldsExcept('address_region') .'|string',
			'country'          => 'nullable|'.$this->requiredLegalFieldsExcept('country') .'|string',
			'vat_number'       => 'nullable|'.$this->requiredLegalFieldsExcept('vat_number') .'|string|max:20',
			'new_password'     => 'nullable|string|min:8|confirmed',
			'send_newsletter'  => 'boolean'
		]);

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

		// Save user
		$user->save();
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
			'address_line1',
			'address_city',
			'address_postcode',
			'address_region',
			'country',
			'vat_number'
		];

		$filtered = array_filter($fields, function($field) use ($name) {
			return $field != $name;
		});

		return 'required_with:'. implode(',', $filtered);
	}
}