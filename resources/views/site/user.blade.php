@extends('site.layout')

@section('title', 'Profilo utente')
@section('body_class', 'page-user')

@section('content')
<pg-navbar variant="dark"></pg-navbar>

<div class="navbar bg-light">
	<div class="container">
		<nav class="nav nav-pills">
			<a class="nav-item nav-link active" href="#">Profilo</a>
			<a class="nav-item nav-link disabled" href="#">Gestione attività</a>
			<a class="nav-item nav-link" href="#">Modifica dati</a>
		</nav>
	</div>
</div>

<div class="container my-5">
	<div class="row">
		<div class="col-md-8">
			<h2>Ciao {{ $user->name }}!</h2>
			<ul class="list-inline text-muted">
				<li class="list-inline-item">{{ $user->email }}</li>
				<li class="list-inline-item">Utente registrato dal {{ $user->created_at->format('j F Y') }}</li>
			</ul>
		</div>
	</div>
</div>

<div class="container my-5">
	<div class="row">
		<div class="col-md-8 col-lg-6 mx-auto">
			<h2>Modifica i tuoi dati</h2>
			<form method="post">
				<div class="form-group">
					<label>Nome completo</label>
					<input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
				</div>
				<div class="form-group">
					<label>E-mail</label>
					<input type="email" class="form-control" value="{{ $user->email }}" disabled>
				</div>
				<div class="form-group">
					<label>Telefono</label>
					<input type="text" class="form-control" name="phone" value="">
				</div>
				<div class="form-group">
					<label>Codice iscrizione AAMS</label>
					<input type="text" class="form-control" name="aams_subject_enrollment_code" value="{{ $user->aams_subject_enrollment_code }}" required>
					<div class="form-text text-muted">Necessario per inserire la tua attività.</div>
				</div>
				<div class="form-group">
					<label>Nuova password</label>
					<input type="password" class="form-control">
				</div>
				<div class="form-group">
					<label>Ripeti nuova password</label>
					<input type="password" class="form-control">
				</div>
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="user-agree-1">
					<label class="custom-control-label" for="user-agree-1">Acconsento al trattamento dei dati personali</label>
				</div>
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="user-agree-2">
					<label class="custom-control-label" for="user-agree-2">Voglio ricevere la newsletter di {{ config('app.name') }}</label>
				</div>
				<div class="form-group mt-3">
					<button type="submit" class="btn btn-primary">Salva</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="container my-5">
	<h2>Gestione attività</h2>
	<div class="row">
		@foreach ($venues as $venue)
			<div class="col-md-6 col-xl-4 mb-4">
				<a href="{{ route('site.venues.edit', $venue) }}" class="h-100">
					<div class="card h-100 flex-row">
						@if ($venue->photos->first())
							<div class="embed-responsive embed-responsive-2by3" style="background: url({{ $venue->photos->first()->thumbnail_url }}); background-size: cover; background-position: center center; width: 160px">
							</div>
						@else
							<div class="embed-responsive embed-responsive-2by3 bg-light d-flex justify-content-center align-items-center" style="width: 160px;"></div>
						@endif
						<div class="card-body d-flex flex-column justify-content-center">
							<h4 class="card-title font-weight-bold">{{ $venue->name }}</h4>
							<div class="card-subtitle mb-2 text-muted initialism">{{ $venue->categories()->first()->name }}</div>
							<p class="card-text">
								@foreach ($venue->addressComponents() as $line)
									{{ $line }}<br>
								@endforeach
							</p>
							<p class="card-text small text-muted">Ultima modifica: {{ $venue->updated_at->format('j F Y, H:m') }}</p>
						</div>
					</div>
				</a>
			</div>
		@endforeach
		<div class="col-md-6 col-xl-4">
			<a href="{{ route('site.venues.create') }}" class="d-block h-100">
				<div class="card h-100 flex-row">
					<div class="embed-responsive embed-responsive-2by3 bg-light d-flex justify-content-center align-items-center" style="width: 160px;">
						<pg-icon icon="plus"></pg-icon>
					</div>
					<div class="card-body d-flex flex-column justify-content-center align-items-center">
						<h4 class="card-text">Aggiungi un'altra attività</h4>
					</div>
				</div>
			</a>
		</div>
	</div>
</div>

<pg-page-footer></pg-page-footer>
@endsection
