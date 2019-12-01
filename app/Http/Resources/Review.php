<?php

namespace App\Http\Resources;

use App\Http\Resources\UserSimple as UserSimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Review extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function toArray($request)
	{
		return [
			'id' => $this->id,
			'title' => $this->title,
			'body' => $this->body,
			'rating' => $this->rating,
			'language' => $this->language,
			'reply' => $this->reply,
			'created_at' => $this->created_at,
			'replied_at' => $this->replied_at,
			'user' => $this->whenLoaded('user', new UserSimpleResource($this->user))
		];
	}
}
