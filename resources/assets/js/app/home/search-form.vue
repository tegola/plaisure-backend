<template>
	<form class="form-search" :action="action" method="get" @submit="onSubmit">
		<input type="hidden" name="lat" :value="latitude">
		<input type="hidden" name="lng" :value="longitude">
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
						@input="loadVenueSuggestions"
						@select="selectVenueSuggestion">
					</pg-input-typeahead>
				</div>
			</div>
			<div class="col-xs-12 col-md-5 col-lg-4">
				<div class="form-group dropdown">
					<label class="initialism"><strong>Vicino a</strong></label><br>
					<div style="position: relative">
						<gmap-autocomplete
							class="form-control form-control-lg search-form-control search-near-control"
							ref="locationAutocomplete"
							name="near"
							placeholder="Città"
							autofocus
							:value="locationQuery"
							:options="locationAutocompleteOptions"
							@place_changed="selectLocationSuggestion">
						</gmap-autocomplete>
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
</template>

<script>
	import Vue from 'vue'
	import VenueSearchFormMixin from '../mixins/venue-search-form'

	export default Vue.extend({
		mixins: [VenueSearchFormMixin]
	})
</script>