@extends('site.layout')

@section('content')
@include('site.venues._navbar', ['show_search' => false])
<div class="container my-5">
	<div class="text-center mb-5">
		<h1 class="display-4">Iscriviti a {{ config('app.name') }}</h1>
		<p class="lead text-muted">Registrandoti potrai salvare le sale preferite e ricevere bonus nelle sale convenzionate.</p>
	</div>

	<div class="row">
		<div class="offset-md-2 col-md-8 offset-xl-3 col-xl-6">
			<form role="form" method="post" action="{{ url('/register') }}">
				{{ csrf_field() }}

				<div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
					<label for="name">Inserisci il tuo nome</label>
					<input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
					@if ($errors->has('name'))
						<div class="form-control-feedback">{{ $errors->first('name') }}</div>
					@endif
				</div>

				<div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
					<label for="email">La tua email</label>
					<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
					@if ($errors->has('email'))
						<div class="form-control-feedback">{{ $errors->first('email') }}</div>
					@endif
				</div>

				<div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
					<label for="password">Scegli una password</label>
					<input id="password" type="password" class="form-control" name="password" required>

					@if ($errors->has('password'))
						<div class="form-control-feedback">{{ $errors->first('password') }}</div>
					@endif
				</div>

				<div class="form-group">
					<label for="password-confirm">Conferma la password (scrivila di nuovo!)</label>
					<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
				</div>

				<div class="form-group">
					<p><button type="submit" class="btn btn-primary btn-block">Crea account</button></p>
				</div>

				<p class="text-center mb-0">oppure <a href="{{ route('site.home') }}">torna all'home page</a></p>
			</form>
		</div>
	</div>
</div>
@endsection
