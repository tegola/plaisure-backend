@php
	if (!isset($fluid)) $fluid = false;
	if (!isset($class)) $class = 'navbar-dark';
	if (!isset($show_search)) $show_search = true;
	if (!isset($vue_support)) $vue_support = false;
	if (!isset($show_location_button)) $show_location_button = false;
@endphp

<nav class="navbar navbar-expand-md {{ $class }}">
	@if (!$fluid)
	<div class="container">
	@endif
		<a class="navbar-brand" href="{{ route('site.home') }}" aria-label="{{ config('app.name') }}">
			@include('site.vectors.logo', [
				'text' => false,
				'class' => 'navbar-logo navbar-logo--no-text'
			])
			@include('site.vectors.logo', [
				'class' => 'navbar-logo'
			])
		</a>
		@if ($show_search)
			<pg-navbar-search-form
				action="{{ route('site.venues.explore') }}"
				@if ($vue_support)
					:query="searchParams.near"
					:center="{ lat: searchParams.c_lat, lng: searchParams.c_lng }"
					:auto-submit="false"
					@place-changed="onPlaceChanged"
				@endif
				>
			</pg-navbar-search-form>
		@endif
		<div class="ml-auto">
			{{ isset($right) ? $right : null }}
			{{--
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
						@if(Gate::allows('administer'))
							<a class="dropdown-item" href="{{ route('admin.home') }}">
								Vai all'amministrazione
							</a>
						@endif
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('navbar-user-logout-form').submit();">Esci</a>
						<form id="navbar-user-logout-form" action="{{ url('/logout') }}" method="POST" hidden>
							{{ csrf_field() }}
						</form>
					</div>
				</span>
			@endif
			--}}
		</div>
	@if (!$fluid)
	</div>
	@endif
</nav>