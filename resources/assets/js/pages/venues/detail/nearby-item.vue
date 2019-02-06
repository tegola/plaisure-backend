<script>
import PgVenueItemMixin from '@/mixins/venue-collection-item';
export default {
	name: 'PgVenueDetailPageNearbyItem',

	mixins: [PgVenueItemMixin]
};
</script>

<template>
	<li class="pg-venue-detail-page__nearby-item mb-2">
		<router-link
			:to="{ name: 'venues.detail', params: { venueId: venue.id } }"
			class="text-inherit d-flex align-items-start">
			<div
				class="pg-venue-detail-page__nearby-item-icon"
				v-html="iconMarkup"
			/>
			<div>
				<span class="pg-venue-detail-page__nearby-item-category">{{ categories }}</span><br>
				<strong class="pg-venue-detail-page__nearby-item-name">{{ venue.name }}</strong><br>
				<span class="pg-venue-detail-page__nearby-item-address">{{ address }}</span>
			</div>
		</router-link>
	</li>
</template>