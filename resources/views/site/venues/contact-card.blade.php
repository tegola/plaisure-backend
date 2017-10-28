<div class="card contact-card {{ isset($class) ? $class : null }}">
	{{-- Map --}}
	<div class="embed-responsive embed-responsive-16by9 contact-card-map">
		<pg-map class="embed-responsive-item" :center="{ lat: {{ $venue->geo_latitude }}, lng: {{ $venue->geo_longitude }} }" :zoom="15" :options="mapOptions">
			@php
				$img = $venue->first_category_machine_name ?: 'collapsed';
			@endphp
			<pg-map-marker :position="{ lat: {{ $venue->geo_latitude }}, lng: {{ $venue->geo_longitude }} }" icon="{{ asset("img/map/pin-normal-{$img}.svg") }}"></pg-map-marker>
		</pg-map>
	</div>

	<div class="list-group list-group-flush">
		{{-- Address --}}
		<div class="list-group-item contact-card-list-item">
			@if (!$venue->isManaged())
				<a href="{{ route('site.promote') }}" class="float-right">modifica</a>
			@endif
			@include ('site.icons.icon', ['name' => 'directions', 'class' => 'contact-card-list-item-icon'])
			<p class="mb-2">
				<strong>{{ $venue->name }}</strong><br>
				@foreach ($venue->addressComponents() as $line)
					{{ $line }}<br>
				@endforeach
			</p>
			<p class="mb-0"><a href="{{ $venue->googleMapsUrl() }}" target="_blank">Come arrivare</a></p>
		</div>

		{{-- Business hours --}}
		<div class="list-group-item contact-card-list-item">
			@if (!$venue->isManaged())
				<a href="{{ route('site.promote') }}" class="float-right">modifica</a>
			@endif

			@if ($venue->businessHours->count())
				@include ('site.icons.icon', ['name' => 'clock-outline', 'class' => 'contact-card-list-item-icon'])
				<a href="#" class="{{ $venue->isOpen() ? 'text-success' : 'text-danger' }}" @click.prevent="toggleHours">
					{{ $venue->isOpen() ? 'Aperto ora' : 'Chiuso ora'}}<pg-icon :icon="hoursIcon" class="ml-1 contact-card-chevron-icon"></pg-icon>
				</a>
				<table v-if="hoursExpanded" v-cloak>
					@foreach($venue->businessHoursByDay() as $dayIndex => $hoursForDay)
						@foreach($hoursForDay as $index => $hours)
							<tr class="{{ $hours->isCurrent() ? 'font-weight-bold' : '' }}">
								<td class="align-top pr-3">
									{{ !$index ? $hours->readableDay() : ''}}
								</td>
								<td>
									@if ($hours->opens && $hours->closes)
										<div class="{{ $hours->isCurrent() ? 'font-weight-bold' : '' }}">{{ $hours->opens }}&ndash;{{ $hours->closes }}</div>
									@else
										<div class="text-muted">Chiuso</div>
									@endif
								</td>
							</tr>
						@endforeach
					@endforeach
				</table>
			@else
				@include ('site.icons.icon', ['name' => 'clock-outline', 'class' => 'contact-card-list-item-icon text-muted'])
				<p class="mb-0 text-muted">
					Nessun orario
				</p>
			@endif

		</div>

		{{-- Contacts --}}
		<div class="list-group-item contact-card-list-item">
			@if (!$venue->isManaged())
				<a href="{{ route('site.promote') }}" class="float-right">modifica</a>
			@endif
			@if ($venue->contact_phone || $venue->contact_email || $venue->contact_facebook || $venue->contact_twitter)
				@include ('site.icons.icon', ['name' => 'phone', 'class' => 'contact-card-list-item-icon'])
				<ul class="list-unstyled mb-0">
					@if ($venue->contact_phone)
						<li><a href="tel://{{ $venue->contact_phone }}">{{ $venue->contact_phone }}</a></li>
					@endif
					@if ($venue->contact_email)
						<li><a href="mailto:{{ $venue->contact_email }}">{{ $venue->contact_email }}</a></li>
					@endif
					@if ($venue->contact_facebook)
						<li><a href="{{ $venue->facebookMessengerUrl() }}" target="_blank">{{ $venue->contact_facebook }}</a> <span class="text-muted">(Facebook Messenger)</span></li>
					@endif
					@if ($venue->contact_twitter)
						<li><a href="{{ $venue->twitterUrl() }}" target="_blank">{{ '@' . $venue->contact_twitter }}</a> <span class="text-muted">(Twitter)</span></li>
					@endif
				</ul>
			@else
				@include ('site.icons.icon', ['name' => 'phone', 'class' => 'contact-card-list-item-icon text-muted'])
				<p class="mb-0 text-muted">
					Nessuna informazione di contatto
				</p>
			@endif
		</div>

		{{-- URLs --}}
		<div class="list-group-item contact-card-list-item">
			@if (!$venue->isManaged())
				<a href="{{ route('site.promote') }}" class="float-right">modifica</a>
			@endif
			@if ($venue->url_site || $venue->url_facebook || $venue->url_tripadvisor)
				@include ('site.icons.icon', ['name' => 'globe', 'class' => 'contact-card-list-item-icon'])
				<ul class="list-unstyled mb-0">
					@if ($venue->url_site)
						<li><a href="{{ $venue->url_site }}" target="_blank">{{ $venue->readableSiteUrl() }}</a></li>
					@endif
					@if ($venue->url_facebook)
						<li><a href="{{ $venue->url_facebook }}" target="_blank">Pagina Facebook</a></li>
					@endif
					@if ($venue->url_tripadvisor)
						<li><a href="{{ $venue->url_facebook }}" target="_blank">Pagina TripAdvisor</a></li>
					@endif
				</ul>
			@else
				@include ('site.icons.icon', ['name' => 'globe', 'class' => 'contact-card-list-item-icon text-muted'])
				<p class="mb-0 text-muted">
					Nessun sito o pagina social
				</p>
			@endif
		</div>
	</div>
</div>