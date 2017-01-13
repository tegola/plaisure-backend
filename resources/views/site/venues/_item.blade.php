<div class="row result" data-lat="{{ $venue->geo_latitude }}" data-lng="{{ $venue->geo_longitude }}">
	<div class="col-12">
		<img class="result-icon" src="{{ asset("img/avatars/{$venue->category_icon_name}") }}">
		<h5 class="mb-0"><a href="{{ route('site.venues.detail', ['venue' => $venue]) }}">{{ $venue->name }}</a></h5>
		<p class="mb-0">
			@if ($venue->distance)
				<strong>{{ $venue->formatted_distance }}</strong> &ndash;
			@endif
			{{ $venue->short_address }}
		</p>
		<ul class="list-inline small text-uppercase text-muted mb-0">
			@if ($venue->categories->count())
				<li class="list-inline-item">{{ $venue->categories->first()->name }}</li>
			@endif
			<li class="list-inline-item">{{ $venue->surface_size }} mq.</li>
			<li class="list-inline-item">{{ $venue->estimated_machine_number }} macchine</li>
		</ul>
	</div>
</div>