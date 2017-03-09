@php
	if (!isset($fluid)) $fluid = false;
	if (!isset($class)) $class = 'navbar-dark navbar-slim';
	if (!isset($show_search)) $show_search = true;
@endphp

<nav class="navbar navbar-toggleable-md {{ $class }}">
	@if (!$fluid)
	<div class="container">
	@endif
		<a class="navbar-brand" href="{{ route('site.home') }}" aria-label="{{ config('app.name') }}">
			@include('site.vectors.logo', ['class' => 'navbar-logo'])
		</a>
		<form class="navbar-search" action="{{ route('site.venues.explore') }}">
			<input type="hidden" name="c_lat" :value="searchParams.c_lat">
			<input type="hidden" name="c_lng" :value="searchParams.c_lng">
			<input type="hidden" name="ne_lat" :value="searchParams.ne_lat">
			<input type="hidden" name="ne_lng" :value="searchParams.ne_lng">
			<input type="hidden" name="sw_lat" :value="searchParams.sw_lat">
			<input type="hidden" name="sw_lng" :value="searchParams.sw_lng">

			<gmap-autocomplete
				class="form-control form-control-lg navbar-search-form-control"
				ref="locationAutocomplete"
				name="near"
				placeholder="Cerca vicino a..."
				:value="searchParams.near"
				:options="{ types: ['geocode'] }"
				@place_changed="onSuggestionSelect">
			</gmap-autocomplete>
		</form>
		<div>
			@if (Auth::guest())
				<a class="btn btn-outline-neutral" href="{{ url('/login') }}">Accedi</a>
				<a class="btn btn-secondary" href="{{ url('/register') }}">Iscriviti</a>
			@else
				<span class="dropdown open">
					<button class="btn btn-secondary dropdown-toggle" type="button" id="navbar-user-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</button>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbar-user-button">
						<a class="dropdown-item" href="{{ route('site.user') }}">
							<strong>{{ Auth::user()->name }}</strong><br>
							<span class="text-muted">Visualizza il tuo profilo</span>
						</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('navbar-user-logout-form').submit();">Esci</a>
						<form id="navbar-user-logout-form" action="{{ url('/logout') }}" method="POST" hidden>
							{{ csrf_field() }}
						</form>
					</div>
				</span>
			@endif
		</div>
	@if (!$fluid)
	</div>
	@endif
</nav>