@extends('admin.layout')

@section('title', 'Esercizi obsoleti')

@section('content')

<div class="container my-5">
	<h3>@yield('title')</h3>
	@if($venues->total())
		<p class="text-muted mb-4">
			{{ $venues->total() }} esercizi non trovati nell'ultima importazione dei dati AAMS.
		</p>
	@endif

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