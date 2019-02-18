<template>
	<div :class="classes" class="list-group-item venue-list-item" @mouseover="onMouseOver" @mouseout="onMouseOut" @click="onClick">
		<div class="row align-items-center">
			<div class="col-3 pr-0">
				<pg-image-frame
					:src="photo ? photo.resized_url : null"
					:content-class="photo ? null : 'pg-venue-grid-item__image-content'"
					class="pg-venue-grid-item__image">
					<div
						v-if="!photo"
						class="pg-venue-grid-item__image-icon"
						v-html="iconMarkup"
					/>
				</pg-image-frame>
			</div>
			<div class="col-9">
				<div class="d-flex w-100 justify-content-between">
					<p class="mb-0 font-weight-bold">
						<router-link :to="{ name: 'venues.detail', params: { venueId: venue.id } }" class="text-inherit">{{ venue.name }}</router-link>
					</p>
					<div v-if="venue.distance" class="text-muted ml-3 text-nowrap">
						{{ venue.distance | formatDistance }}<br>
					</div>
				</div>
				<p v-if="venue.categories.length" class="small text-uppercase text-muted mb-1">{{ categories }}</p>
				<p class="mb-0">{{ address }}</p>
			</div>
		</div>
	</div>
</template>

<script>
import PgVenueItemMixin from '@/mixins/venue-collection-item';
import PgImageFrame from '@/components/image-frame';
import formatDistance from '@/utilities/format-distance';

export default {
	components: {
		PgImageFrame
	},

	filters: {
		formatDistance: formatDistance
	},

	mixins: [PgVenueItemMixin],

	props: {
		highlighted: {
			type: Boolean,
			default: false
		},
		selected: {
			type: Boolean,
			default: false
		}
	},

	computed: {
		classes() {
			return {
				'venue-list-item--highlighted': this.highlighted,
				'active': this.selected
			};
		}
	},

	methods: {
		onMouseOver() {
			this.$emit('mouseover');
		},
		onMouseOut() {
			this.$emit('mouseout');
		},
		onClick() {
			this.$emit('click');
		}
	}
};
</script>
