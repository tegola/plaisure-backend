<div class="row">
	<div class="col-xs-3 col-sm-4">
		<img src="http://placehold.it/300x200" class="img-fluid">
	</div>
	<div class="col-xs-9 col-sm-8">
		<h4><a href="{{ route('site.detail', ['venue' => $venue]) }}">{{ $venue->name }}</a></h4>
		@if ($venue->categories->count())
			<p>
				@foreach ($venue->categories as $category)
					{{ $category->name }}
				@endforeach
			</p>
		@endif
		<p><strong>{{ round($venue->distance) }} km</strong> &ndash; {{ $venue->short_address }}</p>
		<ul class="list-inline">
			<li class="list-inline-item">{{ $venue->surface_size }} mq.</li>
			<li class="list-inline-item">Informazione 2</li>
			<li class="list-inline-item">Informazione 3</li>
		</ul>
		<p class="text-muted">{{ $venue->address }}</p>
	</div>
</div>