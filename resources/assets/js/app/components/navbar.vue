<script>
import { mapState } from 'vuex';
import _extend from 'lodash/extend';

import BNavbarNav from 'bootstrap-vue/es/components/navbar/navbar-nav';
import BNavItem from 'bootstrap-vue/es/components/nav/nav-item';

import PgLogo from './logo';
import PgIcon from './icon';
import PgPlaceTextbox from './place-textbox';

import constants from 'prontogioco/constants';

export default {
	name: 'PgNavbar',

	components: {
		BNavbarNav,
		BNavItem,
		PgLogo,
		PgIcon,
		PgPlaceTextbox
	},

	props: {
		variant: {
			type: String,
			default: 'light'
		},
		placeholder: {
			type: String,
			default() {
				return this.$t('components.navbar.search');
			}
		},
		query: {
			type: String,
			default: null
		},
		center: {
			type: Object,
			default: () => null
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

	computed: {
		...mapState('user', ['user']),
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

	watch: {
		query() {
			this.mutableQuery = this.query;
		}
	},

	beforeCreate() {
		_extend(this, constants);
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
	<nav :class="`navbar-${variant}`" class="navbar navbar-expand">
		<router-link :aria-label="APP_NAME" class="navbar-brand" to="/">
			<pg-logo :text="showLogoText" :class="['navbar__logo', !showLogoText ? 'navbar__logo--no-text' : '']" />
		</router-link>

		<form ref="form" class="navbar__search" action="/venues/explore">
			<input :value="lat" type="hidden" name="c_lat">
			<input :value="lng" type="hidden" name="c_lng">

			<span class="navbar__search-icon-container">
				<pg-icon icon="search" class="navbar__search-icon" />
			</span>
			<pg-place-textbox
				:placeholder="placeholder"
				:place="mutableQuery"
				:options="{ types: ['geocode'] }"
				class="form-control form-control-lg navbar__search-textbox"
				name="query"
				@place-changed="onPlaceChanged"
			/>
		</form>

		<div class="ml-auto d-flex">
			<slot name="right" />
			<b-navbar-nav v-if="user">
				<b-nav-item :to="{ name: 'user' }" exact>
					<pg-icon icon="user" />
					{{ user.name }}
				</b-nav-item>
			</b-navbar-nav>
		</div>
	</nav>
</template>