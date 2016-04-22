@extends('admin.layout')

@section('title', 'Carica esercizi')

@section('content')

<div class="container">
	<div class="page-header">
		<h3>Manutenzione esercizi</h3>
	</div>

	@foreach ($errors->all() as $error)
		<div class="alert alert-warning">
			{{ $error }}
		</div>
	@endforeach

	<p>Pippo</p>
</div>

@endsection