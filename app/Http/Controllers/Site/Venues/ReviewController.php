<?php

namespace App\Http\Controllers\Site\Venues;

use App\Http\Controllers\Controller;
use App\Http\Resources\Venue as VenueResource;
use App\Models\Review;
use App\Models\Venue;
use App\Notifications\Admin\ReviewWithCommentAdded;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ReviewController extends Controller
{
	public function __construct()
	{
		$this
			->middleware('auth:api')
			->except('index', 'report');
	}

	/**
	 * Load the reviews page data.
	 *
	 * @param  Venue   $venue
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function index(Venue $venue, Request $request)
	{
		$venue->load([
			'photos' => function($query) {
				$query->take(1);
			},
			'categories',
			'reviews' => function($query) {
				$query->latest()->take(5);
			}
		]);

		return [
			'venue' => new VenueResource($venue)
		];
	}

	/**
	 * Store a new review or update the previously created one for for the
	 * specified venue.
	 * 
	 * @param  Venue  $venue
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Venue $venue, Request $request) {
		$this->authorize('review', $venue);

		$user = auth()->user();

		// Validate
		$request->validate([
			'rating' => 'required|integer|between:1,5',
			'title' => 'nullable|string',
			'body' => 'required_with:title|string'
		]);

		// Find user review or create a new one
		$review = $venue->reviews()->firstOrNew([
			'user_id' => $user->id
		]);

		DB::transaction(function() use($user, $venue, $review, $request) {
			// Associate to user (if not already present)
			if (!$review->user) $review->user()->associate($user);

			// Associate to venue (if not already present)
			if (!$review->venue) $review->venue()->associate($venue);

			// Save rating
			$review->rating = $request->input('rating');

			// Save text if passed
			if ($request->title && $request->body) {
				$review->title = $request->title;	
				$review->body = $request->body;	
			}

			// Save language if passed
			$review->language = $request->language ?: '';

			$review->save();
		});

		// Notify admin if review needs approval
		if ($request->title && $request->body) {
			$notification = new ReviewWithCommentAdded($review);
			Notification::route('slack', env('SLACK_ACTIVITY_WEBHOOK_URL'))->notify($notification);
		}

		return ['id' => $review->id];
	}

	/**
	 * Reply to a review.
	 *
	 * @param  Venue   $venue
	 * @param  Review  $review
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function reply(Venue $venue, Review $review, Request $request) {
		$this->authorize('reply', $review);

		// Validate
		$request->validate([
			'reply' => 'nullable|string'
		]);

		// Save reply
		$review->update([
			'reply' => $request->reply ?: '',
			'replied_at' => $request->reply ? now() : null
		]);
	}

	/**
	 * Report a review.
	 * 
	 * @param  Venue  $venue
	 * @param  Review $review
	 * @return \Illuminate\Http\Response
	 */
	public function report(Venue $venue, Review $review)
	{
		$review->report_count = $review->report_count + 1;
		$review->save();
	}
}