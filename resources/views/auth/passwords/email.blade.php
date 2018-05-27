@extends('site.layout')

@section('content')
<pg-navbar></pg-navbar>

<div class="container my-5">
	<div class="text-center mb-5">
		<h2>Reimposta password</h2>
		<p class="lead text-muted">Inserisci il tuo indirizzo email per ricevere un link per reimpostare la tua password.</p>
	</div>

	<div class="row">
		<div class="ml-md-auto mr-md-auto col-md-6 col-xl-4">
			@if (session('status'))
				<div class="alert alert-success">
					{{ session('status') }}
				</div>
			@endif

			<form class="form-horizontal" role="form" method="post" action="{{ url('/password/email') }}">
				{{ csrf_field() }}

				<div class="row">
					<div class="col-sm-8 form-group {{ $errors->has('email') ? ' has-danger' : '' }}">
						<label for="email" class="sr-only">Indirizzo email</label>
						<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Indirizzo email" required autofocus>
						@if ($errors->has('email'))
							<div class="form-control-feedback">{{ $errors->first('email') }}</div>
						@endif
					</div>
					<div class="form-group col-sm-4 form-group">
						<button type="submit" class="btn btn-primary btn-block">Invia</button>
					</div>
				</div>
			</form>

			<p class="card-text">Se non ricevi l'email entro pochi minuti, controlla che non sia finita nella posta indesiderata.</p>
		</div>
	</div>
</div>

<pg-page-footer></pg-page-footer>
@endsection
