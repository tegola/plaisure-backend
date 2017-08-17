<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class AboutController extends Controller
{
	public function company() {
		return view('site.about.company');
	}

	public function contact() {
		return view('site.about.contact');
	}
}