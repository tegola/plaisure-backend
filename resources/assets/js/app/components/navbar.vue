<template>
	<nav class="navbar navbar-expand-md" :class="classes">
		<a class="navbar-brand" href="/" :aria-label="appName">
			<pg-logo :text="showLogoText" :class="['navbar-logo', !showLogoText ? 'navbar-logo--no-text' : '']"></pg-logo>
		</a>

		<form class="navbar__search" :action="action" ref="form">
			<input type="hidden" name="c_lat" :value="lat">
			<input type="hidden" name="c_lng" :value="lng">

			<div class="input-group navbar__search-group">
				<span class="input-group-addon navbar__search-icon-container">
					<pg-icon icon="search" class="navbar__search-icon"></pg-icon>
				</span>
				<pg-place-textbox
					class="form-control form-control-lg navbar__search-textbox"
					:placeholder="placeholder"
					:place="mutableQuery"
					:options="{ types: ['geocode'] }"
					@place-changed="onPlaceChanged">
				</pg-place-textbox>
			</div>
		</form>

		<div class="ml-auto">
			<slot name="right"></slot>
		</div>
	</nav>
</template>

<script>
import PgLogo from './logo';
import PgIcon from './icon';
import PgPlaceTextbox from './place-textbox';

export default {
	name: 'pg-navbar',

	components: {
		PgLogo,
		PgIcon,
		PgPlaceTextbox
	},

	props: {
		classes: {
			type: String,
			default: 'navbar-dark'
		},
		action: {
			type: String,
			default: null
		},
		placeholder: {
			type: String,
			default: 'Cerca vicino a...'
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
			appName: pg.app.name,
			mutableQuery: this.query,
			mutableCenter: this.center
		};
	},

	watch: {
		query() {
			this.mutableQuery = this.query;
		}
	},

	computed: {
		showLogoText() {
			return this.$mq.comfortable;
		},
		lat() {
			this.mutableCenter && this.mutableCenter.lat ? this.mutableCenter.lat : null;
		},
		lng() {
			this.mutableCenter && this.mutableCenter.lng ? this.mutableCenter.lng : null;
		}
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
					this.$refs.form.submit();
				});
				return;
			}

			this.$emit('place-changed', place);
		}
	}

};
</script>