@extends('admin.layout')

@section('title', 'Utenti')

@section('content')

<div class="container my-5">
	<div class="row align-items-center mb-4">
		<div class="col">
			<h3>@yield('title')</h3>
			@if($users->total())
				<p class="text-muted mb-2 mb-md-0">
					{{ $users->total() }} {{ old('query') ? 'trovati' : 'totali' }}
				</p>
			@endif
		</div>

		<div class="col-md-8 col-lg-6">
			<form class="d-flex">
				<div class="mr-auto w-100">
					<label class="sr-only">Nome</label>
					<input type="text" class="form-control" name="query" value="{{ old('query') }}" placeholder="Cerca nome, e-mail o codice soggetto AAMS&hellip;">
				</div>
				<button type="submit" class="btn btn-primary ml-2">Cerca</button>
				<a class="btn btn-secondary ml-2 {{ old('query') ? '' : 'disabled' }}" href="{{ route('admin.users.index') }}" title="Reimposta ricerca" data-toggle="tooltip">
					<i class="fa fa-undo"></i>
				</a>
			</form>
		</div>
	</div>

	@if($users->total())

		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>Nome</th>
						<th>E-mail</th>
						<th>Codice soggetto AAMS</th>
						<th class="text-right">Data di registrazione <i class="fa fa-caret-down"></i></th>
					</tr>
				</thead>
				<tbody>
					@foreach($users as $user)
						<tr>
							<td>
								<strong>{{ $user->name }}</strong>
							</td>
							<td>
								<a href="mailto:{{ $user->email }}" title="Scrivi a {{ $user->email }}" data-toggle="tooltip">{{ $user->email }}</a>
							</td>
							<td>
								{{ $user->aams_subject_enrollment_code }}
							</td>
							<td class="text-right">
								{{ $user->created_at->format('j F Y') }}
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>

		{{ $users->links('admin.components.pagination') }}

	@else
		
		<h4 class="text-center text-muted my-5 py-5">Nessun utente trovato.</h4>

	@endif

</div>

@endsection