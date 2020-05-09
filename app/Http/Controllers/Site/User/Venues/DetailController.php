<?php

namespace App\Http\Controllers\Site\User\Venues;

use App\Http\Controllers\Controller;
use App\Http\Resources\Amenity as AmenityResource;
use App\Http\Resources\Concessionaire as ConcessionaireResource;
use App\Http\Resources\File as FileResource;
use App\Http\Resources\Review as ReviewResource;
use App\Http\Resources\Venue as VenueResource;
use App\Http\Resources\VenueCategory as VenueCategoryResource;
use App\Http\Resources\Visit as VisitResource;
use App\Http\Resources\VltPlatform as VltPlatformResource;
use App\Models\Amenity;
use App\Models\Concessionaire;
use App\Models\File;
use App\Models\Venue;
use App\Models\VenueBusinessHour;
use App\Models\VenueCategory;
use App\Models\VltPlatform;
use Carbon;
use Illuminate\Http\Request;


class DetailController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth:api');

		$this->middleware(function($request, $next) {
			$this->authorize('update', $request->venue);

			return $next($request);
		});
	}

	/**
	 * Load the venue detail with categories, photo, visits, etc.
	 *
	 * @param  Venue $venue
	 * @return \Illuminate\Http\Response
	 */
	public function load(Venue $venue)
	{
		// Eager load relationships
		$venue->load([
			'businessHours',
			'categories',
			'amenities',
			'photos',
			'vltPlatforms',
			'subscriptions'
		]);

		return new VenueResource($venue);
	}

	/**
	 * Return the venue overview with visits and favorites.
	 *
	 * @param  Venue $venue
	 * @return \Illuminate\Http\Response
	 */
	public function overview(Venue $venue)
	{
		// Load visits in the specified date
		$visits = $venue->visits()
			->whereBetween('date', [
				Carbon::yesterday()->subDays(28),
				Carbon::yesterday()
			])
			->get(['date', 'count']);

		// Load total visit count
		$visitCount = (int) $venue->visits()->sum('count');

		// Load favorite count
		$favoriteCount = $venue->favoritedBy()->count();

		return compact('visits', 'visitCount', 'favoriteCount');
	}

	/**
	 * Load data for the general section.
	 *
	 * @param  Venue $venue
	 * @return \Illuminate\Http\Response
	 */
	public function general(Venue $venue)
	{
		$categories = VenueCategoryResource::collection(VenueCategory::all());
		$concessionaires = ConcessionaireResource::collection(Concessionaire::all());

		return compact('categories', 'concessionaires');
	}

	/**
	 * Save the venue general section (name, categories, etc.).
	 *
	 * @param  Venue   $venue
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveGeneral(Venue $venue, Request $request)
	{
		$this->validate($request, [
			'concessionaire_id' => 'nullable|exists:concessionaires,id',
			'name'              => 'required|string',
			'description'       => 'nullable|string',
			'surface_size'      => 'nullable|numeric|min:0',
			'address_line1'     => 'required|string',
			'address_line2'     => 'nullable|string',
			'address_city'      => 'required|string',
			'address_postcode'  => 'required|string',
			'address_province'  => 'required|string',
			'country'           => 'required|string',
			'geo_latitude'      => 'required|numeric|between:-90,90',
			'geo_longitude'     => 'required|numeric|between:-180,180',
			'category_ids'      => 'required|array|exists:venue_categories,id',
		]);

		$venue->update([
			'concessionaire_id' => $request->concessionaire_id,
			'name' => $request->name,
			'description' => $request->description ?: '',
			'surface_size' => $request->surface_size ?: 0,
			'address_line1' => $request->address_line1,
			'address_line2' => $request->address_line2 ?: '',
			'address_city' => $request->address_city,
			'address_postcode' => $request->address_postcode,
			'address_province' => $request->address_province,
			'country' => $request->country,
			'geo_latitude' => $request->geo_latitude,
			'geo_longitude' => $request->geo_longitude,
		]);
		$venue->categories()->sync($request->category_ids);
	}

	/**
	 * Load data for the services section.
	 *
	 * @param  Venue $venue
	 * @return \Illuminate\Http\Response
	 */
	public function services(Venue $venue)
	{
		$vltPlatforms = VltPlatformResource::collection(VltPlatform::all());
		$amenities = AmenityResource::collection(Amenity::all());

		return compact('vltPlatforms', 'amenities');
	}

	/**
	 * Save the venue services section.
	 *
	 * @param  Venue   $venue
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveServices(Venue $venue, Request $request)
	{
		$this->validate($request, [
			'vlt_machine_count' => 'nullable|numeric|min:0',
			'awp_machine_count' => 'nullable|numeric|min:0',
			'seating_capacity'  => 'nullable|numeric|min:0',
			'sports_betting'    => 'boolean',
			'virtual_betting'   => 'boolean',
			'horse_betting'     => 'boolean',
			'arcade_roulette'   => 'boolean',
			'amenity_ids'       => 'array|exists:amenities,id',
			'vlt_platform_ids'  => 'array|exists:vlt_platforms,id',
		]);

		$venue->update([
			'sports_betting' => $request->sports_betting ? true : false,
			'virtual_betting' => $request->virtual_betting ? true : false,
			'horse_betting' => $request->horse_betting ? true : false,
			'arcade_roulette' => $request->arcade_roulette ? true : false,
			'vlt_machine_count' => $request->vlt_machine_count ?: 0,
			'awp_machine_count' => $request->awp_machine_count ?: 0,
			'seating_capacity' => $request->seating_capacity ?: 0,
			'parking_capacity' => $request->parking_capacity ?: 0,
		]);
		$venue->amenities()->sync($request->amenity_ids);
		$venue->vltPlatforms()->sync($request->vlt_platform_ids);
	}

	/**
	 * Save the venue contacts section.
	 *
	 * @param  Venue   $venue
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveContacts(Venue $venue, Request $request)
	{
		$this->validate($request, [
			'contact_phone'     => 'nullable|string',
			'contact_email'     => 'nullable|email',
			'contact_facebook'  => 'nullable|string',
			'contact_twitter'   => 'nullable|string',
			'url_site'          => 'nullable|url',
			'url_online_casino' => 'nullable|url',
			'url_facebook'      => 'nullable|url',
			// 'url_tripadvisor'   => 'nullable|url',
		]);

		$venue->update([
			'contact_phone' => $request->contact_phone ?: '',
			'contact_email' => $request->contact_email ?: '',
			'contact_facebook' => $request->contact_facebook ?: '',
			'contact_twitter' => $request->contact_twitter ?: '',
			'url_site' => $request->url_site ?: '',
			'url_online_casino' => $request->url_online_casino ?: '',
			'url_facebook' => $request->url_facebooks ?: ''
			// 'url_tripadvisor => $request->url_tripadvisor ?: ''
		]);
	}

	/**
	 * Save the venue business hours section.
	 *
	 * @param  Venue   $venue
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveBusinessHours(Venue $venue, Request $request)
	{
		$this->validate($request, [
			'business_hours' => 'required|array',
		]);

		$venue->businessHours()->delete();

		foreach ($request->business_hours as $day => $hours) {
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
	}

	/**
	 * Load data for the photos section.
	 *
	 * @param  Venue $venue
	 * @return \Illuminate\Http\Response
	 */
	public function photos(Venue $venue)
	{
		$photos = $venue->photos;

		return FileResource::collection($photos);
	}

	/**
	 * Save the venue photos section.
	 *
	 * @param  Venue   $venue
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function savePhotos(Venue $venue, Request $request)
	{
		$this->validate($request, [
			'photos' => 'array'
		]);

		// Photos
		$photosInput = collect($request->photos);
		$photoIds = $photosInput->pluck('id')->all();

		// Delete photos not in post params (i.e. that were deleted)
		$venue
			->photos()
			->whereNotIn('id', $photoIds)
			->each(function($photo) {
				$photo->delete(); // Delete model + files
			});

		// Save new photos and update existing ones using only orphan files
		// or files already belonging to the venue
		foreach ($photosInput as $index => $photoInput) {
			$photo = File::where('id', $photoInput['id'])
				->whereIn('type', [File::TYPE_UNKNOWN, File::TYPE_VENUE_PHOTO])
				->whereIn('filable_id', [0, $venue->id])
				->first();

			$photo->fill([
				'caption' => $photoInput['caption'] ?: '',
				'type' => File::TYPE_VENUE_PHOTO,
				'order' => $index
			]);
			$photo->filable()->associate($venue);
			$photo->save();

			// Make photo public
			$photo->makePublic();
		}
	}

	/**
	 * Return the venue reviews.
	 *
	 * @param  Venue $venue
	 * @return \Illuminate\Http\Response
	 */
	public function reviews(Venue $venue)
	{
		$reviews = $venue->reviews()
			->withComment()
			->with('user')
			->latest()
			->get();

		return ReviewResource::collection($reviews);
	}

	/**
	 * Save the venue jackpots section.
	 *
	 * @param  Venue   $venue
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function saveJackpots(Venue $venue, Request $request)
	{
		$this->validate($request, [
			'jackpot1_value' => 'nullable|numeric',
			'jackpot1_label' => 'nullable|string',
			'jackpot2_value' => 'nullable|numeric',
			'jackpot2_label' => 'nullable|string',
			'jackpot3_value' => 'nullable|numeric',
			'jackpot3_label' => 'nullable|string',
		]);

		$venue->update([
			'jackpot1_value' => $request->jackpot1_value ?: 0,
			'jackpot1_label' => $request->jackpot1_label ?: '',
			'jackpot2_value' => $request->jackpot2_value ?: 0,
			'jackpot2_label' => $request->jackpot2_label ?: '',
			'jackpot3_value' => $request->jackpot3_value ?: 0,
			'jackpot3_label' => $request->jackpot3_label ?: ''
		]);
	}
}