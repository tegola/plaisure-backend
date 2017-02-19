<template>
	<form class="form-search dropdown" :action="action" method="get">
		<input type="hidden" name="lat" :value="lat">
		<input type="hidden" name="lng" :value="lng">
		<div class="row">
			<div class="col-xs-6 col-md-5 col-lg-5">
				<div class="form-group mb-0 dropdown">
					<pg-input-typeahead
						ref="whatTypeahead"
						classes="form-control form-control-lg"
						name="what"
						placeholder="Trova sale VLT, Bingo, ricevitorie..."
						autofocus
						url="/venues/suggestions"
						:value="query"
						@select="selectSuggestion">
					</pg-input-typeahead>
				</div>
			</div>
			<div class="col-xs-6 col-md-4 col-lg-3 pl-md-0">
				<div class="form-group mb-0 dropdown">
					<input type="text" class="form-control form-control-lg" name="near" :value="near" placeholder="Vicino a..." autocomplete="off">
				</div>
			</div>
			<div class="col-xs-12 col-md-2 pl-md-0">
				<button type="submit" class="btn btn-accent btn-lg" :disabled="!lat || !lng">
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