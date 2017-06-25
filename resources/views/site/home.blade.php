@extends('site.layout')

@section('body_class', 'page-home')

@section('scripts')
<script src="{{ mix('js/app/home.js') }}"></script>
@endsection

@section('content')

<pg-home-page inline-template>
	<div>
		<div class="hero">
			<pg-map class="map" :center="center" :zoom="zoom" :options="mapOptions"></pg-map>
			<nav class="navbar navbar-transparent">
				<div class="container d-flex justify-content-between align-items-center">
					<a class="navbar-brand" href="{{ route('site.home') }}" aria-label="{{ config('app.name') }}">
						@include('site.vectors.logo', ['style' => 'dark', 'class' => 'navbar-logo'])
					</a>
					<div>
						<span class="navbar-age-warning">
							<span class="badge navbar-age-warning-badge" aria-hidden="true">18+</span>
							<span class="navbar-age-warning-text">Il gioco &egrave; vietato<br>ai minori di 18 anni</span>
						</span>
						@if (Auth::guest())
							<a class="btn btn-inverse-neutral" href="{{ url('/login') }}">Accedi</a>
							<a class="btn btn-primary" href="{{ url('/register') }}">Iscriviti</a>
						@else
							<span class="dropdown">
								<button class="btn btn-secondary dropdown-toggle" type="button" id="navbar-user-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</button>
								<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbar-user-button">
									<a class="dropdown-item" href="{{ route('site.user') }}">
										<strong>
											{{ Auth::user()->name }}
											{{ Gate::allows('administer') ? '(amministratore)' : '' }}
										</strong><br>
										<span class="text-muted">Visualizza il tuo profilo</span>
									</a>
									@if(Gate::allows('administer'))
										<a class="dropdown-item" href="{{ route('admin.home') }}">
											Vai all'amministrazione
										</a>
									@endif
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('navbar-user-logout-form').submit();">Esci</a>
									<form id="navbar-user-logout-form" action="{{ url('/logout') }}" method="POST" hidden>
										{{ csrf_field() }}
									</form>
								</div>
							</span>
						@endif
					</div>
				</div>
			</nav>

			<div class="container hero-content">
				<div class="text-center">
					@include('site.vectors.logo', ['text' => false, 'class' => 'logo', 'style' => 'dark'])
					<div class="row">
						<div class="col-xs-12 offset-lg-2 col-lg-8">
							<h1>Cerca le sale da gioco più vicine a te, trova i jackpot più alti e&nbsp;vinci!</h1>
							<p>Più di 5000 sale tra cui&nbsp;scegliere!</p>
						</div>
					</div>
				</div>

				<form class="form-search" action="{{ route('site.venues.explore') }}" method="get" @submit="onSubmit">
					<input type="hidden" name="category" v-model="category">
					<input type="hidden" name="c_lat" v-model="center.lat">
					<input type="hidden" name="c_lng" v-model="center.lng">
					<input type="hidden" name="ne_lat" v-model="ne.lat">
					<input type="hidden" name="ne_lng" v-model="ne.lng">
					<input type="hidden" name="sw_lat" v-model="sw.lat">
					<input type="hidden" name="sw_lng" v-model="sw.lng">
					<div class="row">
						<div class="col-xs-12 offset-md-1 col-md-5 col-lg-4">
							<div class="form-group">
								<label class="initialism"><strong>Trova</strong></label><br>
								<pg-input-typeahead
									classes="form-control form-control-lg search-form-control"
									name="what"
									placeholder="VLT, Bingo, Ricevitoria"
									autofocus
									:value="venueQuery"
									:suggestions="venueSuggestions"
									item-component="pg-venue-suggestion-item"
									@input="onWhatInput"
									@select="selectVenueSuggestion">
								</pg-input-typeahead>
							</div>
						</div>
						<div class="col-xs-12 col-md-5 col-lg-4">
							<div class="form-group dropdown">
								<label class="initialism"><strong>Vicino a</strong></label><br>
								<div style="position: relative">
									<pg-map-autocomplete
										class="form-control form-control-lg search-form-control search-near-control"
										ref="locationAutocomplete"
										name="near"
										placeholder="Città"
										autofocus
										:value="locationQuery"
										:options="locationAutocompleteOptions"
										@place_changed="selectLocationSuggestion">
									</pg-map-autocomplete>
									<button type="button" class="btn btn-lg btn-link search-locate-btn" data-toggle="tooltip" title="Usa la tua posizione" aria-label="Usa la tua posizione" @click="locate" :disabled="isLocateButtonDisabled" tabindex="-1">
										<pg-icon :icon="locateButtonIcon" :spinning="isSearchingLocation"></pg-icon>
									</button>
								</div>
							</div>
						</div>
						<div class="col-xs-12 offset-md-1 col-md-10 offset-lg-0 col-lg-2">
							<div class="form-group">
								<label class="initialism hidden-md-down">&nbsp;</label>
								<button type="submit" class="btn btn-lg btn-block btn-accent search-submit-btn" :disabled="isSubmitButtonDisabled">
									<pg-icon icon="search"></pg-icon>
									Cerca
								</button>
							</div>
						</div>
					</div>
				</form>
			</div>
			<br>
			<br>
			<br>
		</div>
	</div>
</pg-home>
@endsection