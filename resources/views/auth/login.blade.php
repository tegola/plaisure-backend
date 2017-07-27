@extends('site.layout')

@section('content')
@include('site.components.navbar', ['show_search' => false])

<div class="container my-5">
	<div class="text-center mb-5">
		<h2>Accedi</h2>
		<p class="lead text-muted">Inserisci email e password per accedere a {{ config('app.name') }}.</p>
	</div>

	<div class="row">
		<div class="ml-md-auto mr-md-auto col-md-6 col-xl-4">
			<form role="form" method="post" action="{{ url('/login') }}">
				{{ csrf_field() }}

				<div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
					<label for="email">Indirizzo email</label>
					<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
					@if ($errors->has('email'))
						<div class="form-control-feedback">{{ $errors->first('email') }}</div>
					@endif
				</div>

				<div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
					<label for="password">Password</label>
					<a class="float-right" href="{{ url('/password/reset') }}">Password dimenticata?</a>
					<input id="password" type="password" class="form-control" name="password" required>
					@if ($errors->has('password'))
						<div class="form-control-feedback">{{ $errors->first('password') }}</div>
					@endif
				</div>

				<div class="form-group">
					<label class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" name="remember">
						<span class="custom-control-indicator"></span>
						<span class="custom-control-description">Resta connesso</span>
					</label>
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-primary btn-block">Accedi</button>
				</div>
			</form>

			<p class="text-center mb-0">
				
			</p>
		</div>
	</div>
</div>
@endsection
