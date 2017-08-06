@extends('admin.layout')

@section('title', 'Esercizi da gestire')

@section('content')

<div class="container my-5">
	<div class="row align-items-center mb-4">
		<div class="col">
			<h3>@yield('title')</h3>
			@if($importedVenues->total())
				<p class="text-muted mb-2 mb-md-0">
					{{ $importedVenues->total() }} esercizi {{ old('query') ? 'trovati' : 'totali' }} da gestire nell'ultima importazione dei dati AAMS.
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
				<a class="btn btn-secondary ml-2 {{ old('query') ? '' : 'disabled' }}" href="{{ route('admin.venues.unmanaged.index') }}" title="Reimposta ricerca" data-toggle="tooltip">
					<i class="fa fa-undo"></i>
				</a>
			</form>
		</div>
	</div>

	@if($importedVenues->total())

		<table class="table">
			<thead>
				<tr>
					<th>Nome <i class="fa fa-caret-down"></i></th>
					<th>Citt&agrave;</th>
					<th>Codice AAMS</th>
					<th>Codice soggetto AAMS</th>
				</tr>
			</thead>
			<tbody>
				@foreach($importedVenues as $importedVenue)
					<tr>
						<td>
							<strong><a href="{{ route('admin.venues.unmanaged.promote', [$importedVenue]) }}">{{ $importedVenue->name }}</a></strong>
						</td>
						<td>
							{{ $importedVenue->address_1 }}
							-
							{{ $importedVenue->address_2 }}
						</td>
						<td>
							{{ $importedVenue->aams_census_code }}
						</td>
						<td>
							{{ $importedVenue->aams_subject_enrollment_code }}
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		{{ $importedVenues->links('admin.components.pagination') }}

	@else
		
		<h4 class="text-center text-muted my-5 py-5">Nessun esercizio trovato</h4>

	@endif

</div>

@endsection