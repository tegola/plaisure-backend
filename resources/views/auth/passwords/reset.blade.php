@extends('site.layout')

@section('content')
<div class="container h-100">
	<div class="row h-100 flex-items-xs-middle">
		<div class="col-xs-12 offset-sm-1 col-sm-10 offset-md-2 col-md-8 offset-lg-3 col-lg-6">

			<div class="card">
				<div class="card-block">
					<h4 class="card-title mb-0">Reimposta password</h4>
				</div>
				<hr class="my-0">
				<div class="card-block">
					<form class="form-horizontal" role="form" method="post" action="{{ url('/password/reset') }}">
						{{ csrf_field() }}

						<input type="hidden" name="token" value="{{ $token }}">

						<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
							<label for="email">Indirizzo email</label>
							<input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>
							@if ($errors->has('email'))
								<span class="help-block">
									<strong>{{ $errors->first('email') }}</strong>
								</span>
							@endif
						</div>

						<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
							<label for="password">Password</label>
							<input id="password" type="password" class="form-control" name="password" required>
							@if ($errors->has('password'))
								<span class="help-block">
									<strong>{{ $errors->first('password') }}</strong>
								</span>
							@endif
						</div>

						<div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
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
	</div>
</div>
@endsection
