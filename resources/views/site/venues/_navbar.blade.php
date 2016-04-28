<nav class="navbar navbar-light bg-faded">
	<div class="container">
		<a class="navbar-brand" href="{{ route('site.home') }}">{{ config('constants.name') }}</a>

		<form class="form-inline pull-xs-left" action="{{ route('site.explore') }}" method="get">
			<input type="hidden" name="lat" value="{{ $lat }}">
			<input type="hidden" name="lng" value="{{ $lng }}">
			<div class="form-group">
				<input type="text" class="form-control" name="what" value="{{ $what }}" placeholder="Sto cercando&hellip;">
			</div>
			<div class="form-group">
				<input type="text" class="form-control" name="near" value="{{ $near }}" placeholder="Citt&agrave;">
			</div>
			<button type="submit" class="btn btn-primary">Cerca</button>
		</form>

		<div class="pull-xs-right">
			<a class="btn btn-secondary-outline" href="#">Accedi</a>
			<a class="btn btn-primary" href="#">Iscriviti</a>
		</div>
	</div>
</nav>