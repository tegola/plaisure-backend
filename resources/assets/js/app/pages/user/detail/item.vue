<script>
import _extend from 'lodash/extend';
import PgButton from 'prontogioco/app/components/button';

export default {
	name: 'PgUserDetailPageItem',

	components: {
		PgButton
	},

	props: {
		venue: {
			type: Object,
			required: true
		}
	},

	computed: {
		imageClass() {
			const classObj = {
				'embed-responsive': true,
				'embed-responsive-2by3': true
			};

			if (!this.venue.photos || !this.venue.photos.length) {
				_extend(classObj, {
					'bg-light': true,
					'd-flex': true,
					'justify-content-center': true,
					'align-items-center': true
				});
			}

			return classObj;
		},

		imageStyle() {
			const styleObj = {
				width: '160px'
			};

			if (this.venue.photos && this.venue.photos[0]) {
				_extend(styleObj, {
					background: `url(${this.venue.photos[0].thumbnail_url})`,
					backgroundSize: 'cover',
					backgroundPosition: 'center center'
				});
			}

			return styleObj;
		},

		category() {
			if (!this.venue.categories || !this.venue.categories.length) return null;

			return this.$t(`db.categories.${this.venue.categories[0].machine_name}`);
		}
	}
};
</script>

<template>
	<div class="card h-100 flex-row">
		<!-- Left side -->
		<div :style="imageStyle" :class="imageClass" />

		<!-- Right side -->
		<div class="card-body d-flex flex-column justify-content-between">
			<!-- Text -->
			<div>
				<h4 class="card-title font-weight-bold">
					<router-link :to="{ name: 'venues.detail', params: { venueId: venue.id } }">{{ venue.name }}</router-link>
				</h4>
				<div class="card-subtitle mb-2 text-muted initialism">{{ category }}</div>
				<p class="card-text">{{ venue.address.short }}</p>
			</div>

			<!-- Edit button -->
			<pg-button :to="{ name: 'venues.edit', params: { venueId: venue.id } }">{{ $t('common.actions.edit') }}</pg-button>
		</div>
	</div>
</template>