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
		variant: {
			type: String,
			default: 'light'
		},
		fluid: {
			type: Boolean,
			default: false
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
		classes() {
			const  variant = this.variant.split(' ');

			return {
				'navbar-slim': variant.indexOf('slim') != -1,
				'navbar-light': variant.indexOf('light') != -1,
				'navbar-dark': variant.indexOf('dark') != -1
			}
		},
		showLogoText() {
			return this.$mq.comfortable;
		},
		lat() {
			const center = this.mutableCenter;
			return center && center.lat ? center.lat : null;
		},
		lng() {
			const center = this.mutableCenter;
			return center && center.lng ? center.lng : null;
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
				this.$nextTick(() => this.$refs.form.submit());
				return;
			}

			this.$emit('place-changed', place);
		}
	}

};
</script>

<template>
	<nav class="navbar navbar-expand-md" :class="classes">
		<div :class="this.fluid ? 'container-fluid' : 'container'">
			<a class="navbar-brand" href="/" :aria-label="appName">
				<pg-logo :text="showLogoText" :class="['navbar__logo', !showLogoText ? 'navbar__logo--no-text' : '']"></pg-logo>
			</a>

			<form class="navbar__search" action="/venues/explore" ref="form">
				<input type="hidden" name="c_lat" :value="lat">
				<input type="hidden" name="c_lng" :value="lng">

				<span class="navbar__search-icon-container">
					<pg-icon icon="search" class="navbar__search-icon"></pg-icon>
				</span>
				<pg-place-textbox
					class="form-control form-control-lg navbar__search-textbox"
					name="query"
					:placeholder="placeholder"
					:place="mutableQuery"
					:options="{ types: ['geocode'] }"
					@place-changed="onPlaceChanged">
				</pg-place-textbox>
			</form>

			<div class="ml-auto">
				<slot name="right"></slot>
			</div>
		</div>
	</nav>
</template>