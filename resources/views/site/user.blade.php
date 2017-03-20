@extends('site.layout')

@section('title', 'Profilo utente')
@section('body_class', 'page-user')

@section('content')

@include('site.venues._navbar')

<div class="container my-5">
	<div class="row">
		<div class="col-md-8">
			<h2>Ciao, {{ $user->name }}!</h2>
			<ul class="list-inline text-muted">
				<li class="list-inline-item">{{ $user->email }}</li>
				<li class="list-inline-item">Utente registrato dal {{ $user->created_at->format('j F Y') }}</li>
			</ul>
		</div>
	</div>
</div>
@endsection
