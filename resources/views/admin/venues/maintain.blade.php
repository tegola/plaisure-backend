@extends('admin.layout')

@section('title', 'Carica esercizi')

@section('scripts')
<script src="{{ mix('js/admin/maintain.js') }}"></script>
@endsection

@section('content')

@{{ prova }}

<div class="container">

	<h3>Manutenzione esercizi</h3>

	@foreach ($errors->all() as $error)
		<div class="alert alert-warning">
			{{ $error }}
		</div>
	@endforeach

	<ul class="nav nav-tabs my-4">
		<li class="nav-item">
			<a class="nav-link {{ $mode == 'new' ? 'active' : '' }}" href="{{ route('admin.venues.maintain', ['mode' => 'new']) }}">
				Aggiungi <span class="hidden-sm-down">nuovi esercizi</span>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ $mode == 'update' ? 'active' : '' }}" href="{{ route('admin.venues.maintain', ['mode' => 'update']) }}">
				Aggiorna <span class="hidden-sm-down">esercizi esistenti</span>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ $mode == 'delete' ? 'active' : '' }}" href="{{ route('admin.venues.maintain', ['mode' => 'delete']) }}">
				Rimuovi <span class="hidden-sm-down">esercizi obsoleti</span> 
			</a>
		</li>
	</ul>

	@if (!$venue)
		<br>
		<br>
		<h5 class="text-muted mb-0 text-center">
			Nessun esercizio da modificare per la modalit&agrave; scelta.
		</h5>
		<br>
		<br>
	@else
		<form action="{{ route('admin.venues.store') }}" method="post">
			{{ csrf_field() }}
			@if ($venue->exists)
				<input type="hidden" name="id" value="{{ $venue->id }}">
			@endif
			<input type="hidden" name="aams_census_code" value="{{ $venue->aams_census_code }}">
			<input type="hidden" name="aams_subject_enrollment_code" value="{{ $venue->aams_subject_enrollment_code }}">

			<h5 class="mt-3">Informazioni</h5>
			<hr>
			<div class="row">
				<div class="form-group col-6 col-lg-3">
					<label>ID</label>
					<p>
						@if ($venue->exists)
							<code>{{ $venue->id }}</code>
						@else
							<span class="text-muted">(Nuovo)</span>
						@endif
					</p>
				</div>
				<div class="form-group co-6 col-lg-3">
					<label>Codice AAMS</label>
					<p><code>{{ $venue->aams_census_code }}</code></p>
				</div>
				<div class="form-group col-6 col-lg-3">
					<label>Codice soggetto AAMS</label>
					<p><code>{{ $venue->aams_subject_enrollment_code }}</code></p>
				</div>
				@if ($venue->updated_at)
					<div class="form-group col-6 col-lg-3">
						<label>Ultimo aggiornamento</label>
						<p>{{ $venue->updated_at }}</p>
					</div>
				@endif
				@if ($venue->exists)
					<div class="form-group col-12">
						<label>Indirizzo esterno</label>
						<p><a href="{{ route('site.venues.detail', array('venue' => $venue)) }}">{{ route('site.venues.detail', array('venue' => $venue)) }}</a></p>
					</div>
				@endif
			</div>

			<h5 class="mt-3">Generale</h5>
			<hr>
			<div class="form-group">
				<label>Nome</label>
				<input type="text" class="form-control" name="name" value="{{ $venue->name }}">
			</div>
			<div class="row">
				<div class="form-group col-md-6">
					<label>Tipo apparecchi</label>
					<select class="form-control" name="machine_type">
						<option value="">Scegli&hellip;</option>
						@foreach ($machine_types as $type)
							<option value="{{ $type }}" {{ $venue->machine_type == $type ? 'selected' : '' }}>{{ $type }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-md-6">
					<label>Superficie (mq.)</label>
					<input type="text" class="form-control" name="surface_size" value="{{ $venue->surface_size }}">
				</div>
			</div>

			<h5 class="mt-3">Categorie</h5>
			<hr>

			<div class="row form-group">
				@foreach ($categories as $category)
					<div class="col-sm-6 col-md-4 col-lg-3">
						<label>
							<input type="checkbox" name="category_id[]" value="{{ $category->id }}" {{ $venue->categories->contains($category) ? 'checked' : '' }}> {{ $category->name }}
						</label>
					</div>
				@endforeach
			</div>

			<h5 class="mt-3">Indirizzo</h5>
			<hr>

			@if (!$venue->exists)
				<div class="form-group">
					<label>Indirizzo originale</label>
					<div class="input-group">
						<input type="text" class="form-control" value="{{ $venue_original_address }}" readonly>
						<span class="input-group-btn">
							<a class="btn btn-secondary btn-block" href="#" data-toggle="geocode" data-address="{{ $venue_original_address }}">Cerca indirizzo</a>
						</span>
					</div>
				</div>
			@endif

			<div class="row">
				<div class="col-lg-7">
					<div class="row">
						<div class="form-group col-8">
							<label>Via</label>
							<input type="text" class="form-control" name="address_street" value="{{ $venue->address_street }}">
						</div>
						<div class="form-group col-4">
							<label>N. civico</label>
							<input type="text" class="form-control" name="address_number" value="{{ $venue->address_number }}">
						</div>
						<div class="form-group col-md-6">
							<label>Città</label>
							<input type="text" class="form-control" name="address_city" value="{{ $venue->address_city }}">
						</div>
						<div class="form-group col-6 col-md-3">
							<label>CAP</label>
							<input type="text" class="form-control" name="address_postcode" value="{{ $venue->address_postcode }}">
						</div>
						<div class="form-group col-6 col-md-3">
							<label>Provincia</label>
							<input type="text" class="form-control" name="address_province" value="{{ $venue->address_province }}">
						</div>
						<div class="form-group col-md-6">
							<label>Regione</label>
							<input type="text" class="form-control" name="address_region" value="{{ $venue->address_region }}">
						</div>
						<div class="form-group col-md-6">
							<label>Stato</label>
							<input type="text" class="form-control" name="address_country" value="{{ $venue->address_country }}">
						</div>
					</div>
					<div class="form-group">
						<label>Posizione</label>
						<div class="row">
							<div class="col-6">
								<input type="text" class="form-control" name="geo_latitude" value="{{ $venue->geo_latitude }}" placeholder="Latitudine" readonly>
							</div>
							<div class="col-6">
								<input type="text" class="form-control" name="geo_longitude" value="{{ $venue->geo_longitude }}" placeholder="Longitudine" readonly>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-5">
					<div class="form-group">
						<label>Posizione esatta (trascina per riposizionare)</label>
						<div class="map" style="height: 296px; border-radius: 5px"></div>
					</div>
				</div>
			</div>

			<div class="form-group text-right my-3">
				@if ($mode == 'new' || $mode == 'update')
					<button type="submit" class="btn btn-primary">{{ $venue->exists ? 'Salva' : 'Aggiungi' }} e continua</button>
				@elseif ($mode == 'delete')
					<button type="submit" class="btn btn-danger">Elimina esercizio</button>
				@endif
			</div>
		</form>
	@endif
</div>

@endsection