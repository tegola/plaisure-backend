<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class PromoteController extends Controller
{
	public function index() {
		$contactHref = implode('', [
			'mailto:',
			config('constants.email.venues'),
			'?subject=',
			rawurlencode('Rivendicazione attività')
		]);
		return view('site.promote', compact('contactHref'));
	}
}