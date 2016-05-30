@extends('site.layout')

@section('body_class', 'page-explore')
@section('title', "Ricevitorie a {$near}")

@section('content')

@include('site.venues._navbar', ['fluid' => 'true'])
<div class="container-fluid">
	<div class="row">
		<div class="col-md-6">
			<h3>Ricevitorie vicino a <strong>{{ $near }}</strong></h3>

			@foreach ($venues as $venue)
				<hr>
				@include('site.venues.item')
			@endforeach
		</div>
		<div class="col-md-6 hidden-sm-down p-r-0">
			<div class="map"></div>
		</div>
	</div>
</div>

@endsection