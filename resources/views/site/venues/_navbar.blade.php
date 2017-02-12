<nav class="navbar navbar-toggleable navbar-main navbar-dark navbar-slim d-md-flex justify-content-between">
	@if (!isset($fluid))
	<div class="container d-md-flex justify-content-between">
	@endif
		<a class="navbar-brand" href="{{ route('site.home') }}" aria-label="{{ config('constants.name') }}">
			@include('site.vectors.logo', ['class' => 'navbar-logo'])
		</a>
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
	@if (!isset($fluid))
	</div>
	@endif
</nav>

<div class="navbar navbar-secondary">
	@if (!isset($fluid))
	<div class="container">
	@endif
		<pg-search-form action="{{ route('site.venues.explore') }}" :lat="lat" :lng="lng" :what="what" :near="near"></pg-search-form>
	@if (!isset($fluid))
	</div>
	@endif
</div>