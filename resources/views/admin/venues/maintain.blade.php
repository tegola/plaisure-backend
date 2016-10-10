@extends('admin.layout')

@section('title', 'Carica esercizi')

@section('content')

<div class="container">

	<div class="row">
		<div class="col-md-9">
			<h3>Manutenzione esercizi</h3>
		</div>
		<div class="col-md-3 text-md-right">
			<button class="btn btn-secondary btn-block hidden-md-up">Mostra linee guida</button>
			<button class="btn btn-secondary hidden-sm-down">Mostra linee guida</button>
		</div>
	</div>

	@foreach ($errors->all() as $error)
		<div class="alert alert-warning">
			{{ $error }}
		</div>
	@endforeach

	<form method="post">
		<h5 class="m-t-3">Informazioni</h5>
		<hr>
		<div class="row">
			<div class="form-group col-sm-3">
				<label>ID</label>
				<p class="form-control-static">
					@if ($venue->exists)
						<code>12345</code>
					@else
						<span class="text-muted">(Nuovo)</span>
					@endif
				</p>
			</div>
			<div class="form-group col-sm-4">
				<label>Codice AAMS</label>
				<p class="form-control-static"><code>{{ $venue->aams_census_code }}</code></p>
			</div>
			<div class="form-group col-sm-5">
				<label>Codice soggetto AAMS</label>
				<p class="form-control-static"><code>{{ $venue->aams_subject_enrollment_code }}</code></p>
			</div>
		</div>

		<h5 class="m-t-3">Generale</h5>
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

		<div class="row m-t-3">
			<div class="col-md-7">
				<h5>Indirizzo</h5>
			</div>
			<div class="col-md-5 text-md-right">
				<a href="#">Trova con Google Maps</a>
			</div>
		</div>
		<hr>
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
			<div class="form-group col-sm-7">
				<label>Città</label>
				<input type="text" class="form-control" name="address_city" value="{{ $venue->address_city }}">
			</div>
			<div class="form-group col-sm-2">
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
		<div class="card card-default">
			<div class="card-block text-xs-center text-muted">
				Qui va la mappa
			</div>
		</div>

		<hr class="m-t-3">

		<div class="form-group text-xs-right">
			<a href="#" class="btn btn-secondary pull-md-left">Salta questo esercizio</a>
			<button type="submit" class="btn btn-primary">{{{ $venue->exists ? 'Salva' : 'Aggiungi' }}} e continua</button>
		</div>
	</form>
</div>

@endsection