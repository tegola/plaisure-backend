<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ListController extends Controller
{
	/**
	 * Shows the user list.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		// Load users sorted by registration date
		$users = User::latest();

		// Search
		if ($request->filled('query')) {
			$query = $request->input('query');

			$users->where('name', 'like', "%{$query}%")
					->orWhere('email', 'like', "%{$query}%")
					->orWhere('aams_subject_enrollment_code', 'like', "%{$query}%");
		}

		// Paginate
		$users = $users->paginate(100);

		$users->appends($request->all());

		// Pass old values
		$request->flash();

		return view('admin.users.list', compact('users'));
	}
}