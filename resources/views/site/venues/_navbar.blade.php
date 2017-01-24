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
		<form class="form-search dropdown" action="{{ route('site.venues.explore') }}" method="get">
			<input type="hidden" name="lat" value="{{ $lat }}">
			<input type="hidden" name="lng" value="{{ $lng }}">
			<div class="row">
				<div class="col-xs-6 col-md-5 col-lg-5">
					<div class="form-group mb-0 dropdown">
						<input type="text" class="form-control form-control-lg" name="what" value="{{ $what }}" placeholder="Trova sale VLT, Bingo, ricevitorie&hellip;" autocomplete="off">
					</div>
				</div>
				<div class="col-xs-6 col-md-4 col-lg-3 pl-md-0">
					<div class="form-group mb-0 dropdown">
						<input type="text" class="form-control form-control-lg" name="near" value="{{ $near }}" placeholder="Vicino a&hellip;" autocomplete="off">
					</div>
				</div>
				<div class="col-xs-12 col-md-2 pl-md-0">
					<button type="submit" class="btn btn-accent btn-lg">
						@include('site.icons.icon', ['name' => 'search'])
						Cerca
					</button>
				</div>
			</div>
		</form>
	@if (!isset($fluid))
	</div>
	@endif
</div>