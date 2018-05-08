<?php

namespace App\Http\Controllers\Site\Venues;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\VenueCategory;
use App\Models\Concessionaire;
use App\Models\VltPlatform;
use App\Models\PayPerViewPlatform;
use App\Transformers\VenueTransformer;
use JavaScript;

class FormController extends Controller
{
	public function __construct()
	{
		$this->middleware(['auth']);
	}

	/**
	 * Create a new venue.
	 * 
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request) {
		$this->authorize('create', Venue::class);

		$venue = new Venue();

		if ($request->ajax()) {
			return $this->load($venue);
		} else {
			return view('site.venues.form', compact('venue'));
		}
	}

	/**
	 * Edit an existing venue.
	 * 
	 * @param  Venue  $venue
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Venue $venue, Request $request) {
		$this->authorize('update', $venue);

		if ($request->ajax()) {
			return $this->load($venue);
		} else {
			return view('site.venues.form', compact('venue'));
		}
	}

	/**
	 * Load the data for adding/editing a venue with the Venue editor.
	 * 
	 * @param  Venue $venue
	 * @return [mixed]
	 */
	public function load(Venue $venue)
	{
		// Eager load relationships
		$venue->load([
			'businessHours',
			'categories',
			'payPerViewPlatforms',
			'photos',
			'vltPlatforms'
		]);

		$venue = fractal($venue, new VenueTransformer())
			->parseIncludes([
				'business_hours',
				'category_ids',
				'pay_per_view_platform_ids',
				'photos',
				'vlt_platform_ids'
			]);
		
		$categories = VenueCategory::select('id', 'name')->get();
		$concessionaires = Concessionaire::select('id', 'name')->get();
		$vltPlatforms = VltPlatform::select('id', 'name')->get();
		$payPerViewPlatforms = PayPerViewPlatform::select('id', 'name')->get();

		return compact(
			'venue',
			'categories',
			'concessionaires',
			'vltPlatforms',
			'payPerViewPlatforms'
		);
	}

	public function store(Request $request)
	{
		$this->authorize('create', Venue::class);

		$venue = new Venue();

		return $this->save($venue, $request);
	}

	public function update(Venue $venue, Request $request)
	{
		$this->authorize('update', $venue);

		return $this->save($venue, $request);
	}

	public function save(Venue $venue, Request $request)
	{
		$request->validate([
			'concessionaire_id'         => 'nullable|exists:concessionaires,id',
			// 'aams_census_code'          => 'required|string',
			'name'                      => 'required|string',
			'description'               => 'nullable|string',
			'surface_size'              => 'nullable|numeric|min:0',
			'vlt_machine_count'         => 'nullable|numeric|min:0',
			'awp_machine_count'         => 'nullable|numeric|min:0',
			'seating_capacity'          => 'nullable|numeric|min:0',
			'parking_capacity'          => 'nullable|numeric|min:0',
			'sports_betting'            => 'boolean',
			'virtual_betting'           => 'boolean',
			'horse_betting'             => 'boolean',
			'arcade_roulette'           => 'boolean',
			// 'machine_type'              => 'nullable|numeric',

			'address.street'            => 'required|string',
			'address.number'            => 'nullable|string',
			'address.city'              => 'required|string',
			'address.postcode'          => 'required|string',
			'address.province'          => 'required|string',
			// 'address_region'            => 'required|string',
			// 'address_country'           => 'required|string',

			'coords.lat'                => 'required|numeric|between:-90,90',
			'coords.lng'                => 'required|numeric|between:-180,180',

			'contacts.phone'            => 'nullable|string',
			'contacts.email'            => 'nullable|email',
			'contacts.facebook'         => 'nullable|string',
			'contacts.twitter'          => 'nullable|string',

			'urls.site'                 => 'nullable|url',
			'urls.online_casino'        => 'nullable|url',
			'urls.facebook'             => 'nullable|url',
			'urls.tripadvisor'          => 'nullable|url',

			'jackpots.1.label'          => 'nullable|string',
			'jackpots.1.value'          => 'nullable|numeric|min:0',
			'jackpots.2.label'          => 'nullable|string',
			'jackpots.2.value'          => 'nullable|numeric|min:0',
			'jackpots.3.label'          => 'nullable|string',
			'jackpots.3.value'          => 'nullable|numeric|min:0',

			'amenities.atm'             => 'boolean',
			'amenities.bar'             => 'boolean',
			'amenities.pay_per_view'    => 'boolean',
			'amenities.pos'             => 'boolean',
			'amenities.private_parking' => 'boolean',
			'amenities.restaurant'      => 'boolean',
			'amenities.security'        => 'boolean',
			'amenities.smoking_area'    => 'boolean',
			'amenities.wifi'            => 'boolean',

			'category_ids'              => 'required|exists:venue_categories,id',
			'vlt_platform_ids'          => 'nullable|exists:vlt_platforms,id',
			'pay_per_view_platform_ids' => 'nullable|exists:pay_per_view_platforms,id',

			'business_hours'            => 'required|array|size:7', // Array of 7 elements
			// 'business_hours.*'          => 'nullable|string', // FIXME: Use a time pattern (up to 24:00)
			// 'business_hours.*.hours'    => 'sometimes|between:2,4' // FIXME: Use a time pattern (up to 24:00)
		]);
	}
}
