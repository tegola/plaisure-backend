<template>
	<div class="list-group-item venue-list-item" :class="classes" @mouseover="onMouseOver" @mouseout="onMouseOut" @click="onClick">
		<div class="row align-items-center">
			<div class="col-3 pr-0">
				<div class="embed-responsive embed-responsive-4by3">
					<div v-if="photo" class="embed-responsive-item venue-list-item-photo" :style="`background-image: url(${photo.resized_url})`"></div>
					<div v-else class="embed-responsive-item venue-list-item-photo">
						<img class="venue-list-item-icon" :src="'/img/avatars/' + venue.first_category_machine_name + '.svg'">
					</div>
				</div>
			</div>
			<div class="col-9">
				<div class="d-flex w-100 justify-content-between">
					<h5 class="mb-0 font-weight-bold">
						<a class="text-inherit" :href="'/venues/' + venue.id">{{ venue.name }}</a>
					</h5>
					<div class="text-muted ml-3 text-nowrap" v-if="venue.distance">
						{{ venue.distance | formatDistance }}<br>
					</div>
				</div>
				<p v-if="venue.categories.length" class="small text-uppercase text-muted mb-1">{{ categories }}</p>
				<p class="mb-0">{{ venue.short_address }}</p>
			</div>
		</div>
	</div>
</template>

<script>
import formatDistance from '../../../utilities/format-distance';

export default {
	props: {
		venue: {
			type: Object,
			required: true
		},
		highlighted: Boolean,
		selected: Boolean
	},

	filters: {
		formatDistance: formatDistance
	},

	computed: {
		classes() {
			return {
				'venue-list-item--highlighted': this.highlighted,
				'active': this.selected
			};
		},
		categories() {
			if (!this.venue.categories || !this.venue.categories.length) return null;

			return this.venue.categories
				.slice(0, 2)
				.map(category => category.name)
				.join(', ');
		},
		photo() {
			if (!this.venue.photos || !this.venue.photos.length) return null;

			return this.venue.photos[0];
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
