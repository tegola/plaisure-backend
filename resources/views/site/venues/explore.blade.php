@extends('site.layout')

@section('body_class', 'page-explore')
@section('title', $what ? "Risultati per {$what} a {$near}" : "Tutti i risultati a {$near}")

@section('content')

@include('site.venues._navbar', ['fluid' => 'true'])

<div class="wrapper">
	<div class="map"></div>

	<div class="venue-list">
		<div class="container-fluid">
			<h5>
				@if($what)
					{{ $venues->count() }}
					{{ $venues->count() == 1 ? 'risultato' : 'risultati' }}
					per &ldquo;<strong>{{ $what }}</strong>&rdquo;
				@else
					Tutti i risultati
				@endif
				a <strong>{{ $near }}</strong>
			</h5>
			<hr>

			<div id="results">
				@foreach ($venues as $venue)
					<div class="venue" data-lat="{{ $venue->geo_latitude }}" data-lng="{{ $venue->geo_longitude }}">
						<img class="venue-icon" src="{{ asset("img/avatars/{$venue->category_icon_name}") }}">

						<div class="venue-body">
							<h5 class="mb-1"><strong><a href="{{ route('site.venues.detail', ['venue' => $venue]) }}">{{ $venue->name }}</a></strong></h5>
							@if ($venue->categories->count())
								<p class="small text-uppercase text-muted">{{ $venue->categories->first()->name }}</p>
							@endif
							<p class="mb-0">
								@if ($venue->distance)
									<strong>{{ $venue->formatted_distance }}</strong> &ndash;
								@endif
								{{ $venue->short_address }}
							</p>
							<ul class="list-inline mb-0 ">
								<li class="list-inline-item"><a class="text-muted" href="#">Mostra sulla mappa</a></li>
								<li class="list-inline-item text-muted">&middot;</li>
								<li class="list-inline-item"><a class="text-muted" href="#">Aggiungi ai preferiti</a></li>
							</ul>
							<hr class="mb-0">
						</div>
					</div>
				@endforeach
			</div>

			@if ($venues->hasMorePages())
				<div class="text-center">
					<a class="btn btn-outline-primary" href="{{ $venues->nextPageUrl() }}" data-action="load-more">Carica altri risultati&hellip;</a>
				</div>
			@endif
		</div>
	</div>
</div>

@endsection