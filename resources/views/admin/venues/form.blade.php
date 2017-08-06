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
					<div class="card-body">
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
							<pg-map class="embed-responsive-item" :center="mapCenter" :zoom="mapZoom">
								<pg-map-marker :position="mapCenter" draggable @drag="onMarkerDrag"></g-map-marker>
							</pg-map>
						</div>
					</div>
				</div>
			</div>

			<h5 class="mt-3">Contatti</h5>
			<hr>

			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label>Telefono</label>
						<input type="tel" class="form-control" name="contact_phone" v-model="venue.contact_phone">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label>E-mail</label>
						<input type="email" class="form-control" name="contact_email" v-model="venue.contact_email">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label>Facebook Messenger</label>
						<input type="text" class="form-control" name="contact_facebook" v-model="venue.contact_facebook">
					</div>
				</div>
			</div>

			<h5 class="mt-3">Indirizzi web</h5>
			<hr>

			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label>Sito web</label>
						<input type="url" class="form-control" name="url_site" v-model="venue.url_site">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label>Pagina Facebook</label>
						<input type="url" class="form-control" name="url_facebook" v-model="venue.url_facebook">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label>Pagina TripAdvisor</label>
						<input type="url" class="form-control" name="url_tripadvisor" v-model="venue.url_tripadvisor">
					</div>
				</div>
			</div>

			<h5 class="mt-3">Dettagli piano</h5>
			<hr>

			<div class="row">
				<div class="form-group col-md-6">
					<label>Scegli un piano</label>
					<select class="form-control" placeholder="Scegli un piano&hellip;" v-model="selectedPlan">
						<option v-for="plan in plans" :value="plan.short_name">@{{ plan.name }}</option>
						<option disabled>─────</option>
						<option value="custom">Personalizza</option>
					</select>
				</div>
				<div class="form-group col-md-6" v-if="venue.plan">
					<label class="d-none d-md-block">&nbsp;</label>
					<button type="button" class="btn btn-danger btn-block" @click="removePlan">Rimuovi piano</button>
				</div>
			</div>

			<div v-if="venue.plan" class="row">
				<input type="hidden" name="plan[name]" :value="venue.plan.name">
				<input type="hidden" name="plan[short_name]" :value="venue.plan.short_name">
				<div class="form-group col-md-6">
					<label>Bonus distanza</label>
					<input type="hidden" name="plan[distance_bonus]" :value="venue.plan.distance_bonus" v-if="planFieldDisabled">
					<input type="range" class="form-control" name="plan[distance_bonus]" v-model="venue.plan.distance_bonus" :disabled="planFieldDisabled">
					@{{ venue.plan.distance_bonus }}%
				</div>
				<div class="form-group col-md-6">
					<label>Limite foto</label>
					<input type="number" class="form-control" name="plan[photo_limit]" v-model="venue.plan.photo_limit" :readonly="planFieldDisabled">
				</div>
				<div class="form-group col-md-6">
					<div class="form-check">
					<label class="form-check-label">
						<input type="hidden" name="plan[hide_nearby_venues]" value="0" v-if="!venue.plan.hide_nearby_venues">
						<input class="form-check-input" type="checkbox" name="plan[hide_nearby_venues]" value="1" v-model="venue.plan.hide_nearby_venues" :disabled="planFieldDisabled">
						Nascondi attività vicine
					</label>
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