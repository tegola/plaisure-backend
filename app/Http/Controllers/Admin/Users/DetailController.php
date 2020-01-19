<?php

namespace App\Http\Controllers\Admin\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class DetailController extends Controller
{
	/**
	 * Get the data to show the user detail page.
	 * 
	 * @param  User  $user
	 * @return Illuminate\Http\Response
	 */
	public function detail(User $user)
	{
		// Load venues
		$user->load('venues');

		return compact('user');
	}
}