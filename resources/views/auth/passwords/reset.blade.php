@extends('site.layout')

@section('content')
<pg-navbar></pg-navbar>

<div class="container my-5">
	<div class="text-center mb-5">
		<h2>Reimposta password</h2>
		<p class="lead text-muted">Inserisci il tuo indirizzo email e la nuova password per reimpostare quest'ultima.</p>
	</div>

	<div class="row">
		<div class="ml-md-auto mr-md-auto col-md-6 col-xl-4">
			<form class="form-horizontal" role="form" method="post" action="{{ url('/password/reset') }}">
				{{ csrf_field() }}

				<input type="hidden" name="token" value="{{ $token }}">

				<div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
					<label for="email">Indirizzo email</label>
					<input id="email" type="email" class="form-control" name="email" value="{{ $email ?? old('email') }}" required autofocus>
					@if ($errors->has('email'))
						<span class="help-block">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
					@endif
				</div>

				<div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
					<label for="password">Password</label>
					<input id="password" type="password" class="form-control" name="password" required>
					@if ($errors->has('password'))
						<span class="help-block">
							<strong>{{ $errors->first('password') }}</strong>
						</span>
					@endif
				</div>

				<div class="form-group{{ $errors->has('password_confirmation') ? ' has-danger' : '' }}">
					<label for="password-confirm">Conferma password</label>
					<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
					@if ($errors->has('password_confirmation'))
						<span class="help-block">
							<strong>{{ $errors->first('password_confirmation') }}</strong>
						</span>
					@endif
				</div>

				<div class="form-group mb-0">
					<button type="submit" class="btn btn-block btn-primary">Reimposta password</button>
				</div>
			</form>
		</div>
	</div>
</div>

<pg-page-footer></pg-page-footer>
@endsection
