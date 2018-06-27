<script>
import _extend from 'lodash/extend';

import PgLogo from './logo';
import PgIcon from './icon';
import PgPlaceTextbox from './place-textbox';

import constants from 'prontogioco/constants';

export default {
	name: 'PgNavbar',

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
		slim: {
			type: Boolean,
			default: false
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
		user() {
			return this.$store.state.user.data;
		},
		classes() {
			return [
				this.slim ? 'navbar-slim' : null,
				`navbar-${this.variant}`
			];
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
		},

		logout() {
			this.$store.dispatch('user/logout')
				.then(response => {
					this.$router.push({ name: 'home' });
				})
		}
	},

	beforeCreate() {
		_extend(this, constants);
	}
};
</script>

<template>
	<nav class="navbar navbar-expand" :class="classes">
		<div :class="this.fluid ? 'container-fluid' : 'container'">
			<router-link class="navbar-brand" to="/" :aria-label="APP_NAME">
				<pg-logo :text="showLogoText" :class="['navbar__logo', !showLogoText ? 'navbar__logo--no-text' : '']" />
			</router-link>

			<div class="navbar__divider"></div>
			<template v-if="user">
				<a @click="logout">Logout {{ user.name }}</a>
			</template>

			<form class="navbar__search" action="/venues/explore" ref="form">
				<input type="hidden" name="c_lat" :value="lat">
				<input type="hidden" name="c_lng" :value="lng">

				<span class="navbar__search-icon-container">
					<pg-icon icon="search" class="navbar__search-icon" />
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