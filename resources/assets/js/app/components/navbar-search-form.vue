<template>
	<form class="navbar-search" :action="action">
		<input type="hidden" name="c_lat" :value="mutableCenter && mutableCenter.lat ? mutableCenter.lat : null">
		<input type="hidden" name="c_lng" :value="mutableCenter && mutableCenter.lng ? mutableCenter.lng : null">

		<div class="input-group navbar-search-input-group">
			<span class="input-group-addon navbar-search-input-group-addon">
				<pg-icon icon="search"></pg-icon>
			</span>
			<pg-place-textbox
				class="form-control form-control-lg navbar-search-form-control"
				name="near"
				placeholder="Cerca vicino a..."
				:place="mutableQuery"
				:value="mutableQuery"
				:options="{ types: ['geocode'] }"
				@place-changed="onPlaceChanged">
			</pg-place-textbox>
		</div>
	</form>
</template>

<script>
import PgIcon from './icon';
import PgPlaceTextbox from './place-textbox';

export default {
	name: 'pg-navbar-search-form',

	components: {
		PgIcon,
		PgPlaceTextbox
	},

	props: {
		action: {
			type: String,
			default: null
		},
		query: {
			type: String,
			default: null
		},
		center: {
			type: Object,
			default: null,
		},
		autoSubmit: {
			type: Boolean,
			default: true
		}
	},

	data() {
		return {
			mutableQuery: this.query,
			mutableCenter: this.center
		};
	},

	methods: {
		onPlaceChanged(place) {
			if (place) {
				const viewport = place.geometry.viewport;
				const center = viewport.getCenter();

				this.mutableCenter = {
					lat: center.lat(),
					lng: center.lng()
				};

				if (place.vicinity && place.name != place.vicinity) {
					this.mutableQuery = `${place.name}, ${place.vicinity}`;
				} else {
					this.mutableQuery = place.name;
				}
			} else {
				this.mutableQuery = null;
				this.mutableCenter = null;
			}

			if (this.autoSubmit) {
				this.$nextTick(() =>{
					this.$el.submit();
				});
				return;
			}

			this.$emit('place-changed', place);
		}
	}
};
</script>