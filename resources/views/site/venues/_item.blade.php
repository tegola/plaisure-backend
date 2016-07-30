<div data-lat="{{ $venue->geo_latitude }}" data-lng="{{ $venue->geo_longitude }}">
	<h5><a href="{{ route('site.venues.detail', ['venue' => $venue]) }}">{{ $venue->name }}</a></h5>

	@if ($venue->categories->count())
		@foreach ($venue->categories as $category)
			<p class="text-muted">{{ $category->name }}</p>
		@endforeach
	@endif

	<p>
		@if ($venue->distance)
			<strong>{{ $venue->formatted_distance }}</strong> &ndash;
		@endif
		{{ $venue->short_address }}
	</p>

	<ul class="list-inline">
		<li class="list-inline-item">{{ $venue->surface_size }} mq.</li>
		<li class="list-inline-item">{{ $venue->estimated_machine_number }} macchine</li>
	</ul>
	<p class="text-muted">{{ $venue->address }}</p>
</div>