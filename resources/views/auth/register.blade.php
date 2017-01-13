@extends('site.layout')

@section('content')
<div class="container h-100">
	<div class="row h-100 align-items-center">
		<div class="col-12 offset-sm-1 col-sm-10 offset-md-2 col-md-8 offset-lg-3 col-lg-6">

			<div class="card">
				<div class="card-block">
					<h4 class="card-title">Registrati a ProntoGioco</h4>
					{{-- FIXME: Aggiornare il testo --}}
					<p class="card-text">Registrandoti a ProntoGioco...</p>
				</div>
				<hr class="my-0">
				<div class="card-block">
					<form class="form-horizontal" role="form" method="post" action="{{ url('/register') }}">
						{{ csrf_field() }}

						<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
							<label for="name">Nome</label>
							<input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
							@if ($errors->has('name'))
								<div class="form-control-feedback">{{ $errors->first('name') }}</div>
							@endif
						</div>

						<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
							<label for="email">Indirizzo email</label>
							<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
							@if ($errors->has('email'))
								<div class="form-control-feedback">{{ $errors->first('email') }}</div>
							@endif
						</div>

						<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
							<label for="password">Password</label>
							<input id="password" type="password" class="form-control" name="password" required>

							@if ($errors->has('password'))
								<div class="form-control-feedback">{{ $errors->first('password') }}</div>
							@endif
						</div>

						<div class="form-group">
							<label for="password-confirm">Conferma password</label>
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
	</div>
</div>
@endsection
