<?php

namespace App\Http\Controllers\Site\Venues;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\VenueCategory;
use App\Models\Concessionaire;
use App\Models\VltPlatform;
use App\Models\PayPerViewPlatform;
use App\Models\VenueBusinessHour;
use App\Models\File;
use App\Models\Subscription;
use App\Transformers\VenueTransformer;
use DB;

class FormController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:api');
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

		return $this->load($venue);
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

		return $this->load($venue);
	}

	/**
	 * Load the data for adding/editing a venue with the Venue editor.
	 *
	 * @param  Venue $venue
	 * @return [mixed]
	 */
	public function load(Venue $venue)
	{
		$user = auth()->user();

		// Eager load relationships
		$venue->load([
			'businessHours',
			'categories',
			'payPerViewPlatforms',
			'photos',
			'vltPlatforms',
			'subscriptions'
		]);

		$venue = fractal($venue, new VenueTransformer())
			->parseIncludes([
				'business_hours',
				'category_ids',
				'pay_per_view_platform_ids',
				'photos',
				'vlt_platform_ids',
				'subscription'
			]);

		$country = locale_get_region($user->locale);
		$categories = VenueCategory::forCountry($country)
			->select('id', 'machine_name')
			->get();
		$concessionaires = Concessionaire::forCountry($country)
			->select('id', 'name')
			->get();
		$vltPlatforms = VltPlatform::forCountry($country)
			->select('id', 'name')
			->get();
		$payPerViewPlatforms = PayPerViewPlatform::forCountry($country)
			->select('id', 'name')
			->get();

		return compact(
			'venue',
			'categories',
			'concessionaires',
			'vltPlatforms',
			'payPerViewPlatforms'
		);
	}

	/**
	 * Store a new Venue.
	 *
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$this->authorize('create', Venue::class);

		$venue = new Venue();

		return $this->save($venue, $request);
	}

	/**
	 * Update an existing Venue.
	 *
	 * @param  Venue   $venue
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function update(Venue $venue, Request $request)
	{
		$this->authorize('update', $venue);

		return $this->save($venue, $request);
	}

	public function save(Venue $venue, Request $request)
	{
		if ($venue->subscribed()) {
			$subscription = $venue->subscription();
		} else {
			$subscription = new Subscription(config('subscriptions.default'));
		}

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

			'address.line1'             => 'required|string',
			'address.line2'             => 'nullable|string',
			'address.city'              => 'required|string',
			'address.postcode'          => 'required|string',
			'address.province'          => 'required|string',
			// 'address.region'            => 'required|string',

			'coords.lat'                => 'required|numeric|between:-90,90',
			'coords.lng'                => 'required|numeric|between:-180,180',

			'contacts.phone'            => 'nullable|string',
			'contacts.email'            => 'nullable|email',
			'contacts.facebook'         => 'nullable|string',
			'contacts.twitter'          => 'nullable|string',

			'urls.site'                 => 'nullable|url',
			'urls.online_casino'        => 'nullable|url',
			'urls.facebook'             => 'nullable|url',
			// 'urls.tripadvisor'          => 'nullable|url',

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

			// 'business_hours'            => 'required|array|size:7', // Array of 7 elements
			// 'business_hours.*'          => 'nullable|string', // FIXME: Use a time pattern (up to 24:00)
			// 'business_hours.*.hours'    => 'sometimes|between:2,4' // FIXME: Use a time pattern (up to 24:00)
			
			'photos'                    => "array|max:{$subscription->photo_limit}"
		]);

		DB::transaction(function() use($venue, $request) {
			$venue->fill([
				// General pane
				'name' => $request->input('name'),
				'concessionaire_id' =>  $request->input('concessioniare_id'),
				'description' =>  $request->input('description') ?: '',
				'surface_size' =>  $request->input('surface_size') ?: 0,
				'address_line1' => $request->input('address.line1'),
				'address_line2' => $request->input('address.line2') ?: '',
				'address_city' => $request->input('address.city'),
				'address_postcode' => $request->input('address.postcode'),
				'address_province' => $request->input('address.province'),
				'geo_latitude' => $request->input('coords.lat'),
				'geo_longitude' => $request->input('coords.lng'),

				// Services pane
				'sports_betting' => $request->input('sports_betting'),
				'virtual_betting' => $request->input('virtual_betting'),
				'horse_betting' => $request->input('horse_betting'),
				'arcade_roulette' => $request->input('arcade_roulette'),
				'vlt_machine_count' => $request->input('vlt_machine_count') ?: 0,
				'awp_machine_count' => $request->input('awp_machine_count') ?: 0,
				'seating_capacity' => $request->input('seating_capacity') ?: 0,
				'parking_capacity' => $request->input('parking_capacity') ?: 0,
				'amenity_atm' => $request->input('amenities.atm'),
				'amenity_bar' => $request->input('amenities.bar'),
				'amenity_pay_per_view' => $request->input('amenities.pay_per_view'),
				'amenity_pos' => $request->input('amenities.pos'),
				'amenity_private_parking' => $request->input('amenities.private_parking'),
				'amenity_restaurant' => $request->input('amenities.restaurant'),
				'amenity_security' => $request->input('amenities.security'),
				'amenity_smoking_area' => $request->input('amenities.smoking_area'),
				'amenity_wifi' => $request->input('amenities.wifi'),

				// Contacts pane
				'contact_phone' => $request->input('contacts.phone') ?: '',
				'contact_email' => $request->input('contacts.email') ?: '',
				'contact_facebook' => $request->input('contacts.facebook') ?: '',
				'contact_twitter' => $request->input('contacts.twitter') ?: '',
				'url_site' => $request->input('urls.site') ?: '',
				'url_online_casino' => $request->input('urls.online_casino') ?: '',
				'url_facebook' => $request->input('urls.facebook') ?: '',

				// Jackpots pane
				'jackpot1_label' => $request->input('jackpots.1.label') ?: '',
				'jackpot1_value' => $request->input('jackpots.1.value') ?: 0,
				'jackpot2_label' => $request->input('jackpots.2.label') ?: '',
				'jackpot2_value' => $request->input('jackpots.2.value') ?: 0,
				'jackpot3_label' => $request->input('jackpots.3.label') ?: '',
				'jackpot3_value' => $request->input('jackpots.3.value') ?: 0
			]);
			$venue->save();

			$venue->categories()->sync($request->category_ids);
			$venue->vltPlatforms()->sync($request->vlt_platform_ids);
			$venue->payPerViewPlatforms()->sync($request->pay_per_view_platform_ids);

			// Business hours
			$venue->businessHours()->delete();
			foreach ($request->input('business_hours') as $day => $hours) {
				if (count($hours) > 0) {
					$split1 = new VenueBusinessHour([
						'day' => $day,
						'opens' => $hours[0],
						'closes' => $hours[1]
					]);
					$venue->businessHours()->save($split1);
				}
				if (count($hours) > 2) {
					$split2 = new VenueBusinessHour([
						'day' => $day,
						'opens' => $hours[2],
						'closes' => $hours[3]
					]);
					$venue->businessHours()->save($split2);
				}
			}

			// Delete old photos, then save new ones by using only orphan files
			// or files already belonging to the venue
			$photoInputCollection = collect($request->input('photos'));
			$photoIds = $photoInputCollection->pluck('id')->all();

			$venue
				->photos()
				->whereNotIn('id', $photoIds)
				->each(function($photo) {
					$photo->delete(); // Delete model + files
				});

			$photos = File::whereIn('id', $photoIds)
				->whereIn('type', [File::TYPE_UNKNOWN, File::TYPE_VENUE_PHOTO])
				->whereIn('filable_id', [0, $venue->id])
				->each(function ($photo) use ($photoInputCollection, $venue) {
					$input = $photoInputCollection->firstWhere('id', $photo->id);

					$photo->caption = $input['caption'] ?: '';
					$photo->type = File::TYPE_VENUE_PHOTO;
					$photo->filable()->associate($venue);
					$photo->save();

					// Make photo public
					$photo->makePublic();
				});

			// Save
			$venue->save();
		});

		return [
			'id' => $venue->id
		];
	}
}
