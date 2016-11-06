@extends('site.layout')

@section('body_class', 'page-explore')
@section('title', $what ? "Risultati per {$what} a {$near}" : "Tutti i risultati a {$near}")

@section('content')

@include('site.venues._navbar', ['fluid' => 'true'])
<div class="container-fluid mt-2">
	<div class="row">
		<div class="col-md-6">
			<h3>
				@if($what)
					{{ $venues->count() }}
					{{ $venues->count() == 1 ? 'risultato' : 'risultati' }}
					per <strong class="text-info">{{ $what }}</strong>
				@else
					Tutti i risultati
				@endif
				a <strong>{{ $near }}</strong>
			</h3>

			<div id="results">
				@foreach ($venues as $venue)
					@include('site.venues._item')
				@endforeach
			</div>

			@if($venues->hasMorePages())
				<a class="btn btn-outline-primary btn-block" href="{{ $venues->nextPageUrl() }}" data-action="load-more">Vedi altri risultati&hellip;</a>
			@endif

		</div>
		<div class="col-md-6 hidden-sm-down pr-0">
			<div class="map"></div>
		</div>
	</div>
</div>

@endsection