<div class="row">
	<div class="col-xs-8 col-sm-9 col-md-8 col-lg-9">
		<h5><a href="{{ route('site.venues.detail', ['venue' => $venue]) }}">{{ $venue->name }}</a></h5>
		<p>
			@if ($venue->distance)
				<strong>{{ $venue->formatted_distance }}</strong> &ndash;
			@endif
			{{ $venue->short_address }}
		</p>
		<ul class="list-inline">
			@if ($venue->categories->count())
				@foreach ($venue->categories as $category)
					<li class="list-inline-item">{{ $category->name }}</li>
				@endforeach
			@endif
			<li class="list-inline-item">{{ $venue->surface_size }} mq.</li>
			<li class="list-inline-item">{{ $venue->estimated_machine_number }} macchine</li>
		</ul>
		<p class="text-muted">{{ $venue->address }}</p>
	</div>
	<div class="col-xs-4 col-sm-3 col-md-4 col-lg-3">
		<img src="http://placehold.it/300x200" class="img-fluid">
	</div>
</div>