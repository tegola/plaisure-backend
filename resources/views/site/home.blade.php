@extends('site.layout')

@section('body_class', 'page-home')

@section('content')

<pg-home-page inline-template>
	<div>
		<div class="hero">
			<pg-map class="map" v-bind="mapProps"></pg-map>
			{{--
			<nav class="navbar navbar-transparent navbar-expand-md">
				<div class="container justify-content-center">
					<a class="navbar-brand" href="{{ route('site.home') }}" aria-label="{{ config('app.name') }}">
						@include('site.vectors.logo', ['style' => 'dark', 'class' => 'navbar-logo'])
					</a>
					<div>
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
			--}}

			<div class="container hero-content">
				<div class="text-center">
					@include('site.vectors.logo', ['class' => 'logo', 'style' => 'dark'])
					<div class="row">
						<div class="col-lg-8 ml-lg-auto mr-lg-auto">
							<h1>Cerca le sale da gioco più vicine a te, trova i jackpot più alti e&nbsp;vinci!</h1>
							<p>Più di 5000 sale tra cui&nbsp;scegliere!</p>
						</div>
					</div>
				</div>

				<form class="form-search" action="{{ route('site.venues.explore') }}" method="get" @submit="onSubmit">
					<input type="hidden" name="categories[]" v-model="categories" v-if="categories.length">
					<input type="hidden" name="c_lat" v-model="searchCenter.lat">
					<input type="hidden" name="c_lng" v-model="searchCenter.lng">
					<div class="row">
						<div class="ml-md-auto col-md-5 col-lg-4">
							<div class="form-group">
								<label class="initialism"><strong>Trova</strong></label><br>
								<pg-input-typeahead
									classes="form-control form-control-lg search-form-control"
									name="what"
									placeholder="VLT, Bingo, Ricevitoria"
									autofocus
									v-model="searchQuery"
									:suggestions="searchSuggestions"
									item-component="pg-venue-suggestion-item"
									@input="onSearchInput"
									@select="onSearchSuggestionSelect">
								</pg-input-typeahead>
							</div>
						</div>
						<div class="col-md-5 col-lg-4 mr-md-auto mr-lg-0">
							<div class="form-group dropdown">
								<label class="initialism"><strong>Vicino a</strong></label><br>
								<div style="position: relative">
									<pg-place-textbox
										class="form-control form-control-lg search-form-control search-near-control"
										ref="placeTextbox"
										name="near"
										placeholder="Città"
										autofocus
										:place="placeQuery"
										:value="placeQuery"
										:disabled="isSearchingLocation"
										:options="placeTextboxOptions"
										@place_changed="onPlaceChanged">
									</pg-place-textbox>
									<button type="button" ref="locateButton" class="btn btn-lg btn-link search-locate-btn" data-toggle="tooltip" title="Usa la tua posizione" aria-label="Usa la tua posizione" @click="locate" :disabled="isSearchingLocation" tabindex="-1">
										<pg-icon :icon="locateButtonIcon" :spinning="isSearchingLocation"></pg-icon>
									</button>
								</div>
							</div>
						</div>
						<div class="col-md-10 ml-md-auto mr-md-auto col-lg-2 ml-lg-0 mr-lg-auto">
							<div class="form-group">
								<label class="initialism d-none d-lg-inline-block">&nbsp;</label>
								<button type="submit" class="btn btn-lg btn-block btn-accent search-submit-btn" :disabled="!canSubmit">
									<pg-icon icon="search"></pg-icon>
									Cerca
								</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="container text-center my-md-5 py-5">
			<div class="row justify-content-stretch">
				<div class="mb-3 mb-md-0 col-md">
					<a class="card h-100" href="{{ route('site.venues.explore') }}">
						<div class="card-body">
							<div><img src="{{ asset('img/home/map.svg') }}"></div>
							<p class="card-text">Ti senti avventuroso?</p>
							<h4 class="card-title">Esplora la tua zona</h4>
						</div>
					</a>
				</div>
				<div class="mb-3 mb-md-0 col-md">
					<a class="card h-100" href="{{ route('site.promote') }}">
						<div class="card-body">
							<div><img src="{{ asset('img/home/venue.svg') }}"></div>
							<p class="card-text">Sei nel campo?</p>
							<h4 class="card-title">Promuovi la tua attivit&agrave;</h4>
						</div>
					</a>
				</div>
				<div class="mb-3 mb-md-0 col-md">
					<a class="card h-100" href="{{ route('site.play-responsibly.index') }}">
						<div class="card-body">
							<div><img src="{{ asset('img/home/machine.svg') }}"></div>
							<p class="card-text">Non esagerare</p>
							<h4 class="card-title">Gioca responsabilmente</h4>
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>
</pg-home>
@endsection