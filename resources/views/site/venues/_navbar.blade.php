<nav class="navbar navbar-full navbar-white">
	<div class="container{{ $fluid or null ? '-fluid' : ''}}">
		<a class="navbar-brand" href="{{ route('site.home') }}">{{ config('constants.name') }}</a>
		<form class="form-inline float-xs-left form-search dropdown" action="{{ route('site.venues.explore') }}" method="get">
			<input type="hidden" name="lat" value="{{ $lat }}">
			<input type="hidden" name="lng" value="{{ $lng }}">
			<div class="form-group dropdown">
				<input type="text" class="form-control" name="what" value="{{ $what }}" placeholder="Trova" autocomplete="off">
			</div>
			<div class="form-group dropdown">
				<input type="text" class="form-control" name="near" value="{{ $near }}" placeholder="Vicino a" autocomplete="off">
			</div>
			<button type="submit" class="btn btn-primary">
				@include('site.icons.icon', ['name' => 'search'])
				<span class="sr-only">Cerca</span>
			</button>
		</form>
		<div class="float-xs-right">
			@if (Auth::guest())
				<a class="btn btn-outline-secondary" href="{{ url('/login') }}">Accedi</a>
				<a class="btn btn-primary" href="{{ url('/register') }}">Registrati</a>
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
	</div>
</nav>