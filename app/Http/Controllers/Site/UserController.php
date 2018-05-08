<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Auth;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
	/**
	 * Show the user dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$user = Auth::user();
		$venues = $user->venues()
			->with([
				'categories',
				'photos' => function($query) {
					$query->first();
				}
			])
			->get();

		return view('site.user', compact('user', 'venues'));
	}
}
