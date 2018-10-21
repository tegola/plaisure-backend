<template>
	<div :class="classes" class="list-group-item venue-list-item" @mouseover="onMouseOver" @mouseout="onMouseOut" @click="onClick">
		<div class="row align-items-center">
			<div class="col-3 pr-0">
				<div class="embed-responsive embed-responsive-4by3">
					<div
						v-if="photo"
						:style="`background-image: url(${photo.resized_url})`"
						class="embed-responsive-item venue-list-item-photo"
					/>
					<div v-else class="embed-responsive-item venue-list-item-photo">
						<img :src="`/img/avatars/${firstCategoryMachineName}.svg`" class="venue-list-item-icon">
					</div>
				</div>
			</div>
			<div class="col-9">
				<div class="d-flex w-100 justify-content-between">
					<h5 class="mb-0 font-weight-bold">
						<router-link :to="{ name: 'venues.detail', params: { venueId: venue.id } }" class="text-inherit">{{ venue.name }}</router-link>
					</h5>
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
import PgVenueItemMixin from 'prontogioco/app/mixins/venue-collection-item';
import formatDistance from 'prontogioco/utilities/format-distance';

export default {
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
