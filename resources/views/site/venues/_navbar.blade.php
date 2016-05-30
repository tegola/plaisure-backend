<nav class="navbar navbar-full navbar-white">
	<div class="container{{ $fluid or null ? '-fluid' : ''}}">
		<a class="navbar-brand" href="{{ route('site.home') }}">{{ config('constants.name') }}</a>
		<form class="form-inline pull-xs-left form-search dropdown" action="{{ route('site.explore') }}" method="get">
			<input type="hidden" name="lat" value="{{ $lat }}">
			<input type="hidden" name="lng" value="{{ $lng }}">
			<div class="form-group dropdown">
				<input type="text" class="form-control" name="what" value="{{ $what }}" placeholder="Trova" autocomplete="off">
			</div>
			<div class="form-group dropdown">
				<input type="text" class="form-control" name="near" value="{{ $near }}" placeholder="Vicino a" autocomplete="off">
			</div>
			<button type="submit" class="btn btn-primary">
				@include('site.icons.icon', ['name' => 'search'])
				<span class="sr-only">Cerca</span>
			</button>
		</form>
		<div class="pull-xs-right">
			<span class="hidden-sm-down">
				<a class="btn btn-secondary-outline" href="#">Accedi</a>
				<a class="btn btn-primary" href="#">Iscriviti</a>
			</span>
		</div>
	</div>
</nav>