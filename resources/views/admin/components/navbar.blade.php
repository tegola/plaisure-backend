<nav class="navbar navbar-toggleable-md navbar-light bg-faded mb-3">
	<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbar-collapse" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Mostra menu di navigazione">
		<span class="navbar-toggler-icon"></span>
	</button>
	<a class="navbar-brand" href="{{ route('admin.home') }}">{{ config('app.name') }} - Amministrazione</a>

	<div class="collapse navbar-collapse" id="navbar-collapse">
		<ul class="navbar-nav">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="http://example.com" id="navbar-venues-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Esercizi
				</a>
				<div class="dropdown-menu" aria-labelledby="navbar-venues-link">
					<a class="dropdown-item" href="{{ route('admin.venues.index') }}">Esercizi attivi</a>
					<a class="dropdown-item" href="#">Esercizi chiusi</a>
					<a class="dropdown-item" href="#">Nuovi esercizi</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{ route('admin.venues.upload') }}">Carica CSV</a>
					{{-- <a class="dropdown-item" href="{{ route('admin.venues.maintain') }}">Modalit&agrave; di manutenzione</a> --}}
				</div>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.users.index') }}">Utenti</a>
			</li>
		</ul>
	</div>
</nav>