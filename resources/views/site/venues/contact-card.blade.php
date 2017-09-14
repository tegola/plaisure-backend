<div class="card contact-card {{ isset($class) ? $class : null }}">
	<div class="embed-responsive embed-responsive-16by9 contact-card-map">
		<pg-map class="embed-responsive-item" :center="{ lat: {{ $venue->geo_latitude }}, lng: {{ $venue->geo_longitude }} }" :zoom="15" :options="mapOptions">
			@php
				$img = $venue->first_category_machine_name ?: 'collapsed';
			@endphp
			<pg-map-marker :position="{ lat: {{ $venue->geo_latitude }}, lng: {{ $venue->geo_longitude }} }" icon="{{ asset("img/map/pin-normal-{$img}.svg") }}"></pg-map-marker>
		</pg-map>
	</div>
	<div class="list-group list-group-flush">
		<div class="list-group-item contact-card-list-item">
			<a href="{{ route('site.promote') }}" class="float-right">modifica</a>
			<pg-icon icon="directions" class="contact-card-list-item-icon"></pg-icon>
			<p class="mb-2">
				<strong>{{ $venue->name }}</strong><br>
				@foreach ($venue->addressComponents() as $line)
					{{ $line }}<br>
				@endforeach
			</p>
			<p class="mb-0"><a href="{{ $venue->googleMapsUrl() }}" target="_blank">Ottieni indicazioni</a></p>
		</div>
		<div class="list-group-item contact-card-list-item">
			<a href="{{ route('site.promote') }}" class="float-right">modifica</a>
			<pg-icon icon="clock-outline" class="contact-card-list-item-icon text-muted"></pg-icon>
			<p class="mb-0 text-muted">
				Nessun orario
			</p>
		</div>
		<div class="list-group-item contact-card-list-item">
			<a href="{{ route('site.promote') }}" class="float-right">modifica</a>
			<pg-icon icon="phone" class="contact-card-list-item-icon text-muted"></pg-icon>
			<p class="mb-0 text-muted">
				Nessuna informazione di contatto
			</p>
		</div>
		<div class="list-group-item contact-card-list-item">
			<a href="{{ route('site.promote') }}" class="float-right">modifica</a>
			<pg-icon icon="globe" class="contact-card-list-item-icon text-muted"></pg-icon>
			<p class="mb-0 text-muted">
				Nessun sito o pagina social
			</p>
		</div>
	</div>
</div>