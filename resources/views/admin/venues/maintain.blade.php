@extends('admin.layout')

@section('title', 'Carica esercizi')

@section('content')

<div class="container">

	<h3>Manutenzione esercizi</h3>
	<ul class="nav nav-tabs mt-1">
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

	@foreach ($errors->all() as $error)
		<div class="alert alert-warning">
			{{ $error }}
		</div>
	@endforeach

	@if (!$venue)
		<br>
		<br>
		<h5 class="text-muted mb-0 text-xs-center">
			Nessun esercizio da modificare per la modalit&agrave; scelta.
		</h5>
		<br>
		<br>
	@else
		<form method="post">
			@if ($venue->exists)
				<input type="hidden" name="id" value="{{ $venue->id }}">
			@endif
			<input type="hidden" name="aams_census_code" value="{{ $venue->aams_census_code }}">
			<input type="hidden" name="aams_subject_enrollment_code" value="{{ $venue->aams_subject_enrollment_code }}">
			<h5 class="mt-3">Informazioni</h5>
			<hr>
			<div class="row">
				<div class="form-group col-xs-6 col-sm-3">
					<label>ID</label>
					<p>
						@if ($venue->exists)
							<code>{{ $venue->id }}</code>
						@else
							<span class="text-muted">(Nuovo)</span>
						@endif
					</p>
				</div>
				<div class="form-group col-xs-6 col-sm-3">
					<label>Codice AAMS</label>
					<p><code>{{ $venue->aams_census_code }}</code></p>
				</div>
				<div class="form-group col-xs-6 col-sm-3">
					<label>Codice soggetto AAMS</label>
					<p><code>{{ $venue->aams_subject_enrollment_code }}</code></p>
				</div>
				@if ($venue->updated_at)
					<div class="form-group col-xs-6 col-sm-3">
						<label>Ultimo aggiornamento</label>
						<p><code>{{ $venue->updated_at }}</code></p>
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
					<label>Categoria</label>
					<select class="form-control">
						<option value="">Scegli&hellip;</option>
						@foreach ($categories as $category)
							<option value="{{ $category->id }}">{{ $category->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-md-3">
					<label>Tipo apparecchi</label>
					<select class="form-control">
						<option value="">Scegli&hellip;</option>
						@foreach ($machine_types as $type)
							<option value="{{ $type }}" {{{ $venue->machine_type == $type ? 'selected' : '' }}}>{{ $type }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-md-3">
					<label>Superficie (mq.)</label>
				<input type="text" class="form-control" name="surface_size" value="{{ $venue->surface_size }}">
				</div>
			</div>

			<div class="mt-3">
				<h5>Indirizzo</h5>
				@if ($venue_original_address)
					<p class="text-muted">Originale: {{ $venue_original_address }} (<a href="#trova">Trova con Google Maps</a>)</p>
				@endif
			</div>
			<hr>

			<div class="row">
				<div class="col-md-7">
					<div class="row">
						<div class="form-group col-sm-8">
							<label>Via</label>
							<input type="text" class="form-control" name="address_street" value="{{ $venue->address_street }}">
						</div>
						<div class="form-group col-sm-4">
							<label>Numero civico</label>
							<input type="text" class="form-control" name="address_number" value="{{ $venue->address_number }}">
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-3">
							<label>CAP</label>
							<input type="text" class="form-control" name="address_postcode" value="{{ $venue->address_postcode }}">
						</div>
						<div class="form-group col-sm-6">
							<label>Città</label>
							<input type="text" class="form-control" name="address_city" value="{{ $venue->address_city }}">
						</div>
						<div class="form-group col-sm-3">
							<label>Provincia</label>
							<input type="text" class="form-control" name="address_province" value="{{ $venue->address_province }}">
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-6">
							<label>Regione</label>
							<input type="text" class="form-control" name="address_region" value="{{ $venue->address_region }}">
						</div>
						<div class="form-group col-sm-6">
							<label>Stato</label>
							<input type="text" class="form-control" name="address_country" value="{{ $venue->address_country }}">
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-6">
							<label>Posizione</label>
							<input type="text" class="form-control" placeholder="Latitudine" name="geo_latitude" value="{{ $venue->geo_latitude }}">
						</div>
						<div class="form-group col-sm-6">
							<label class="hidden-xs-down">&nbsp;</label>
							<input type="text" class="form-control" placeholder="Longitudine" name="geo_longitude" value="{{ $venue->geo_longitude }}">
						</div>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>Mappa</label>
						<div class="card card-default">
							<div class="card-block text-xs-center text-muted">
								Qui va la mappa
							</div>
						</div>
					</div>
				</div>
			</div>

			<hr class="mt-3">

			<div class="form-group text-xs-right">
				@if ($mode == 'new' || $mode == 'update')
					<button type="submit" class="btn btn-primary">{{{ $venue->exists ? 'Salva' : 'Aggiungi' }}} e continua</button>
				@elseif ($mode == 'delete')
					<button type="submit" class="btn btn-danger">Elimina esercizio</button>
				@endif
			</div>
		</form>
	@endif
</div>

@endsection