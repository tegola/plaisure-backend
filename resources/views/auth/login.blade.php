@extends('site.layout')

@section('content')
<div class="container h-100">
	<div class="row h-100 flex-items-xs-middle">
		<div class="col-xs-12 offset-sm-2 col-sm-8 offset-md-3 col-md-6 offset-lg-4 col-lg-4">

			<div class="card">
				<div class="card-block text-xs-center">
					<h4 class="card-title mb-0">Accedi a ProntoGioco</h4>
				</div>
				<hr class="my-0">
				<div class="card-block">
					<form class="form-horizontal" role="form" method="post" action="{{ url('/login') }}">
						{{ csrf_field() }}

						<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
							<label for="email" class="sr-only">Indirizzo email</label>
							<input id="email" type="email" class="form-control form-control-lg" name="email" value="{{ old('email') }}" placeholder="Indirizzo email" required autofocus>
							@if ($errors->has('email'))
								<div class="form-control-feedback">{{ $errors->first('email') }}</div>
							@endif
						</div>

						<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
							<label for="password" class="sr-only">Password</label>
							<input id="password" type="password" class="form-control form-control-lg" name="password" placeholder="Password" required>
							@if ($errors->has('password'))
								<div class="form-control-feedback">{{ $errors->first('password') }}</div>
							@endif
						</div>

						{{--
						<div class="form-group">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="remember"> Ricorda i miei dati
								</label>
							</div>
						</div>
						--}}

						<div class="form-group mb-0">
							<button type="submit" class="btn btn-primary btn-lg btn-block">Accedi</button>
						</div>
					</form>
				</div>
				<hr class="my-0">
				<div class="card-block">
					<p class="text-xs-center mb-0">
						<a href="{{ url('/password/reset') }}">Hai dimenticato la password?</a>
					</p>
				</div>
			</div>

		</div>
	</div>
</div>
@endsection
