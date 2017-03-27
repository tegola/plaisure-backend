<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Auth;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the user dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('site.user', [
			'user' => Auth::user()
		]);
	}
}
