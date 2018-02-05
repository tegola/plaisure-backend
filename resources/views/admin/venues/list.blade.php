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

		<div class="col-md-8">
			<form class="d-flex align-items-center">
				<div class="mr-auto w-100">
					<label class="sr-only">Nome</label>
					<input type="text" class="form-control" name="query" value="{{ old('query') }}" placeholder="Cerca nome, città, provincia o codice AAMS&hellip;">
				</div>
				<label class="form-check-label mb-0 ml-2">
					<input class="form-check-input" type="checkbox" name="without_geo_data" value="1" {{ old('without_geo_data') ? 'checked' : null }}>
					<span class="text-nowrap">Senza dati geografici</span>
				</label>
				<button type="submit" class="btn btn-primary ml-2">Cerca</button>
				<a class="btn btn-secondary ml-2 {{ old('query') || old('without_geo_data') ? '' : 'disabled' }}" href="{{ route('admin.venues.index') }}" title="Reimposta ricerca" data-toggle="tooltip">
					<i class="fa fa-undo"></i>
				</a>
			</form>
		</div>
	</div>

	@if($venues->total())

		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>Nome</th>
						<th>Citt&agrave;</th>
						<th>Codice AAMS</th>
						<th class="text-right text-nowrap">Aggiornato <i class="fa fa-caret-down"></i></th>
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
							<td class="text-right text-nowrap">
								{{ $venue->updated_at->format('j F Y') }}
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>

		{{ $venues->links('admin.components.pagination') }}

	@else
		
		<h4 class="text-center text-muted my-5 py-5">Nessun esercizio trovato</h4>

	@endif

</div>

@endsection