@extends('site.layout')

@section('content')
<div class="container h-100">
	<div class="row h-100 align-items-center">
		<div class="col-12 offset-sm-1 col-sm-10 offset-md-2 col-md-8 offset-lg-3 col-lg-6">

			<div class="card">
				<div class="card-block">
					<h4 class="card-title">Reimposta password</h4>
					<p class="card-text">Inserisci il tuo indirizzo email e provvederemo a inviarti un link per reimpostare la tua password.</p>
				</div>
				<hr class="my-0">
				<div class="card-block">
					@if (session('status'))
						<div class="alert alert-success">
							{{ session('status') }}
						</div>
					@endif

					<form class="form-horizontal" role="form" method="post" action="{{ url('/password/email') }}">
						{{ csrf_field() }}

						<div class="row">
							<div class="col-12 col-md-8 col-xl-9 form-group {{ $errors->has('email') ? ' has-error' : '' }}">
								<label for="email" class="sr-only">Indirizzo email</label>
								<input id="email" type="email" class="form-control form-control-lg" name="email" value="{{ old('email') }}" placeholder="Indirizzo email" required autofocus>
								@if ($errors->has('email'))
									<div class="form-control-feedback">{{ $errors->first('email') }}</div>
								@endif
							</div>
							<div class="form-group col-12 col-md-4 col-xl-3">
								<button type="submit" class="btn btn-primary btn-lg btn-block">Invia</button>
							</div>
						</div>
					</form>

					<p class="card-text">Se non ricevi l'email entro pochi minuti, controlla che non sia finita nella posta indesiderata.</p>
				</div>
			</div>

		</div>
	</div>
</div>
@endsection
