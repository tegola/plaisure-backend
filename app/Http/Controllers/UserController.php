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
			->includePhotos();

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
			'name' => 'required|string|max:255',
			'aams_subject_enrollment_code' => 'required|string|max:255',
			'new_password' => 'nullable|string|min:8|confirmed'
		]);

		// Save user data
		$user->name = $request->input('name');
		$user->aams_subject_enrollment_code = $request->input('aams_subject_enrollment_code');

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
}