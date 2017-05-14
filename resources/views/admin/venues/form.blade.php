@extends('admin.layout')

@section('title', $venue->exists ? 'Modifica esercizio' : 'Aggiungi esercizio')

@section('content')

<pga-venue-form-page inline-template>
	<div class="container my-5">

		<h3 class="mb-5">@yield('title')</h3>

		@foreach ($errors->all() as $error)
			<div class="alert alert-warning">
				{{ $error }}
			</div>
		@endforeach

		<form action="{{ $venue->exists ? route('admin.venues.update', $venue) : route('admin.venues.store') }}" method="POST">
			{{ csrf_field() }}
			@if ($venue->exists)
				{{ method_field('PATCH') }}
				{{-- <input type="hidden" name="id" value="{{ $venue->id }}"> --}}
			@endif
			<input type="hidden" name="aams_census_code" v-model="venue.aams_census_code">
			<input type="hidden" name="aams_subject_enrollment_code" v-model="venue.aams_subject_enrollment_code">

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
				<div class="input-group">
					<input type="text" class="form-control" name="name" v-model="venue.name">
					<span class="input-group-btn">
						<button class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#guidelines" aria-expanded="false" aria-controls="guidelines">Linee guida</button>
					</span>
				</div>
			</div>
			<div id="guidelines" class="collapse">
				<div class="card mb-3">
					<div class="card-block">
						<ul class="mb-0">
							<li>Niente sigle societarie: "srl", "s.r.l.", "SRL", "società a responsabilità...", snc" ecc.</li>
							<li>Niente nomi di persone: "Sala slot di Rossi Mario" &rarr; "Sala slot"</li>
							<li>Evitare i nomi tutti in maiuscolo: "BETTER SCOMMESSE" &rarr; "Better scommesse"</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-md-6">
					<label>Tipo apparecchi</label>
					<select class="form-control" name="machine_type" v-model="venue.machine_type">
						<option value="">Scegli&hellip;</option>
						<option v-for="(name, id) in machineTypes" :value="id">@{{ name }}</option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label>Superficie (mq.)</label>
					<input type="text" class="form-control" name="surface_size" v-model="venue.surface_size">
				</div>
			</div>

			<h5 class="mt-3">Categorie</h5>
			<hr>

			<div class="row form-group">
				<div class="col-sm-6 col-md-4 col-lg-3" v-for="(name, id) in categories">
					<label>
						<input type="checkbox" name="categories[]" :value="id" v-model.number="venueCategories">
						@{{ name }}
					</label>
				</div>
			</div>

			<h5 class="mt-3">Indirizzo</h5>
			<hr>

			<div class="form-group" v-if="importedVenueAddress">
				<label>Indirizzo originale</label>
				<div class="input-group">
					<input type="text" class="form-control" :value="importedVenueAddress" readonly>
					<span class="input-group-btn">
						<button type="button" class="btn btn-secondary btn-block" @click="geocode">Cerca indirizzo</a>
					</span>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-7">
					<div class="row">
						<div class="form-group col-8">
							<label>Via</label>
							<input type="text" class="form-control" name="address_street" v-model="venue.address_street">
						</div>
						<div class="form-group col-4">
							<label>N. civico</label>
							<input type="text" class="form-control" name="address_number" v-model="venue.address_number">
						</div>
						<div class="form-group col-12">
							<label>Città</label>
							<input type="text" class="form-control" name="address_city" v-model="venue.address_city">
						</div>
						<div class="form-group col-4">
							<label>CAP</label>
							<input type="text" class="form-control" name="address_postcode" v-model="venue.address_postcode">
						</div>
						<div class="form-group col-8">
							<label>Provincia</label>
							<input type="text" class="form-control" name="address_province" v-model="venue.address_province">
						</div>
						<div class="form-group col-md-6">
							<label>Regione</label>
							<input type="text" class="form-control" name="address_region" v-model="venue.address_region">
						</div>
						<div class="form-group col-md-6">
							<label>Stato</label>
							<input type="text" class="form-control" name="address_country" v-model="venue.address_country">
						</div>
					</div>
					<div class="form-group">
						<label>Posizione</label>
						<div class="row">
							<div class="col-6">
								<input type="text" class="form-control" name="geo_latitude" v-model="venue.geo_latitude" placeholder="Latitudine" readonly>
							</div>
							<div class="col-6">
								<input type="text" class="form-control" name="geo_longitude" v-model="venue.geo_longitude" placeholder="Longitudine" readonly>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-5">
					<div class="form-group">
						<label>Posizione esatta (trascina per riposizionare)</label>
						<div class="embed-responsive embed-responsive-1by1" style="height: 382px; border-radius: 5px">
							<g-map class="embed-responsive-item" :center="mapCenter" :zoom="mapZoom">
								<g-map-marker :position="mapCenter" draggable @drag="onMarkerDrag"></g-map-marker>
							</g-map>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group text-right my-3">
				<button type="submit" class="btn btn-primary">{{ $venue->exists ? 'Salva' : 'Aggiungi' }}</button>
			</div>
		</form>
			
	</div>
</pga-venue-form-page>

@endsection