@extends('site.layout')

@section('title', 'Profilo utente')
@section('body_class', 'page-user')

@section('content')

<div class="container mt-2 mb-1">
	<div class="row clearfix">
		<div class="col-md-8">
			<h4 class="initialism text-muted">Profilo utente</h4>
			<h2 class="mt-0">{{ $user->name }}</h2>
			<br>
			<dl>
				<dt>Nome</dt>
				<dd>{{ $user->name }}</dd>
				<dt>Indirizzo email</dt>
				<dd>{{ $user->email }}</dd>
				<dt>Data di registrazione</dt>
				<dd>{{ $user->created_at }}</dd>
			</dl>
		</div>
		<div class="col-md-4 text-xs-right">
			<p>
				<a class="btn btn-secondary" href="#">Ottieni indicazioni</a>
				<a class="btn btn-secondary" href="#">Salva</a>
			</p>
		</div>
	</div>
</div>
@endsection
