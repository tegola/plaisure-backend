<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MainController extends Controller
{
	/**
	 * Show the home page.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('site.layout');
	}
}