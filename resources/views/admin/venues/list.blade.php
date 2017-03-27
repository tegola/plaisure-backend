@extends('admin.layout')

@section('title', 'Esercizi')

@section('content')

<div class="container my-5">
	<div class="row align-items-center mb-4">
		<div class="col">
			<h3>@yield('title')</h3>
			@if($venues->total())
				<p class="text-muted mb-2 mb-md-0">
					{{ $venues->total() }} {{ old('query') ? 'trovati' : 'totali' }}
				</p>
			@endif
		</div>

		<div class="col-md-8 col-lg-6">
			<form class="d-flex">
				<div class="mr-auto w-100">
					<label class="sr-only">Nome</label>
					<input type="text" class="form-control" name="query" value="{{ old('query') }}" placeholder="Cerca nome, città, provincia o codice AAMS&hellip;">
				</div>
				<button type="submit" class="btn btn-primary ml-2">Cerca</button>
				<a class="btn btn-secondary ml-2 {{ old('query') ? '' : 'disabled' }}" href="{{ route('admin.venues.index') }}" title="Reimposta ricerca" data-toggle="tooltip">
					<i class="fa fa-undo"></i>
				</a>
			</form>
		</div>
	</div>

	@if($venues->total())

		<table class="table">
			<thead>
				<tr>
					<th>Nome</th>
					<th>Citt&agrave;</th>
					<th>Codice AAMS</th>
					<th class="text-right">Aggiunto</th>
					<th class="text-right">Aggiornato <i class="fa fa-caret-down"></i></th>
				</tr>
			</thead>
			<tbody>
				@foreach($venues as $venue)
					<tr>
						<td>
							<strong><a href="{{ route('admin.venues.edit', $venue) }}">{{ $venue->name }}</a></strong>
						</td>
						<td>
							{{ $venue->address_city }}
							{{ $venue->address_province ? "({$venue->address_province})" : '' }}
						</td>
						<td>
							{{ $venue->aams_census_code }}
						</td>
						<td class="text-right">
							{{ $venue->created_at->format('j F Y') }}
						</td>
						<td class="text-right">
							{{ $venue->updated_at->format('j F Y') }}
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		{{ $venues->links('admin.components.pagination') }}

	@else
		
		<h4 class="text-center text-muted my-5 py-5">Nessun esercizio trovato.</h4>

	@endif

</div>

@endsection