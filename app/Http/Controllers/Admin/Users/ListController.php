<?php

namespace App\Http\Controllers\Admin\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class ListController extends Controller
{
	/**
	 * Get the data to show the user list.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$currentPage = $request->query('currentPage', 1);
		$perPage = $request->query('perPage', 20);
		$sortBy = $request->query('sortBy', 'created_at');
		$sortDir = $request->query('sortDesc', 'true') == 'true' ? 'desc' : 'asc';
		$filter = $request->query('filter');

		$users = User::orderBy($sortBy, $sortDir)
			->when($filter, function($query, $filter) {
				return $query
					->where('id', $filter)
					->orWhere('name', 'like', "%{$filter}%");
			})
			->paginate($perPage, ['*'], 'page', $currentPage);

		return compact('users');
	}
}