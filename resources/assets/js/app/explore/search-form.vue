<template>
	<form class="form-search" :action="action" method="get" @submit="onSubmit">
		<input type="hidden" name="lat" :value="latitude">
		<input type="hidden" name="lng" :value="longitude">
		<div class="row">
			<div class="col-xs-6 col-md-5 col-lg-5">
				<div class="form-group mb-0 dropdown">
					<pg-input-typeahead
						classes="form-control form-control-lg"
						name="what"
						placeholder="Trova sale VLT, Bingo, ricevitorie..."
						:value="venueQuery"
						:suggestions="venueSuggestions"
						item-component="pg-venue-suggestion-item"
						@input="loadVenueSuggestions"
						@select="selectVenueSuggestion">
					</pg-input-typeahead>
				</div>
			</div>
			<div class="col-xs-6 col-md-4 col-lg-3 pl-md-0">
				<div class="form-group mb-0 dropdown">
					<gmap-autocomplete
						class="form-control form-control-lg"
						ref="locationAutocomplete"
						name="near"
						placeholder="Vicino a..."
						autofocus
						:value="locationQuery"
						:options="locationAutocompleteOptions"
						@place_changed="selectLocationSuggestion">
					</gmap-autocomplete>
				</div>
			</div>
			<div class="col-xs-12 col-md-2 pl-md-0">
				<button type="submit" class="btn btn-accent btn-lg" :disabled="isSubmitButtonDisabled">
					<pg-icon icon="search"></pg-icon>
					Cerca
				</button>
			</div>
		</div>
	</form>

</template>

<script>
	import Vue from 'vue';
	import VenueSearchFormMixin from '../mixins/venue-search-form';

	export default Vue.extend({
		mixins: [VenueSearchFormMixin]
	});
</script>