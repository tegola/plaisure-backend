<script>
import 'classlist-polyfill';
import { mapState } from 'vuex';
import _extend from 'lodash/extend';

import BNav from 'bootstrap-vue/es/components/nav/nav';
import BNavbarNav from 'bootstrap-vue/es/components/navbar/navbar-nav';
import BNavItem from 'bootstrap-vue/es/components/nav/nav-item';

import PgLogo from './logo';
import PgIcon from './icon';
import PgPlaceTextbox from './place-textbox';

import constants from '@/constants';

export default {
	name: 'PgNavbar',

	components: {
		BNav,
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
		search: {
			type: Boolean,
			default: true
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
			mutableCenter: this.center,
			drawerOpen: false
		};
	},

	computed: {
		...mapState('user', ['user']),
		classes() {
			return [
				this.drawerOpen ? 'navbar-dark navbar--drawer-open' : `navbar-${this.variant}`,
				'navbar',
				'navbar-expand'
			];
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

	destroyed() {
		this.toggleOverflow(false);
	},

	methods: {
		toggleDrawer(force) {
			const open = force !== undefined ? force : !this.drawerOpen;

			this.drawerOpen = open;
			this.toggleOverflow(open);
		},

		toggleOverflow(open) {
			document.body.classList.toggle('pg--pg-overlay-open', open);
		},

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
	<div>
		<nav :class="classes">
			<!-- Normal logo, goes to home page -->
			<router-link v-if="$mq.comfortable" :aria-label="APP_NAME" class="navbar-brand" to="/">
				<pg-logo class="navbar__logo" />
			</router-link>

			<!-- Small logo, toggles menu -->
			<div v-if="$mq.constrained" :aria-label="APP_NAME" class="navbar-brand" @click="toggleDrawer()">
				<pg-logo :text="false" class="navbar__logo navbar__logo--no-text" />
				<pg-icon icon="chevron-down" class="navbar__logo-arrow" />
			</div>

			<form v-if="search" ref="form" class="navbar__search" action="/venues/explore">
				<input :value="lat" type="hidden" name="c_lat">
				<input :value="lng" type="hidden" name="c_lng">

				<span class="navbar__search-icon-container">
					<pg-icon icon="search" />
				</span>
				<pg-place-textbox
					:placeholder="placeholder"
					:value="mutableQuery"
					:options="{ types: ['geocode'] }"
					class="form-control form-control-lg navbar__search-textbox"
					name="query"
					@focus="toggleDrawer(false)"
					@place-changed="onPlaceChanged"
				/>
			</form>

			<div class="ml-auto d-flex">
				<slot name="right" />
				<b-navbar-nav v-if="$mq.comfortable">
					<template v-if="!user">
						<b-nav-item :to="{ name: 'promote' }">{{ $t('components.navbar.promote') }}</b-nav-item>
						<b-nav-item :to="{ name: 'register' }">{{ $t('components.navbar.register') }}</b-nav-item>
						<b-nav-item :to="{ name: 'login' }">{{ $t('components.navbar.login') }}</b-nav-item>
					</template>
					<b-nav-item v-if="user" :to="{ name: 'user' }" exact>
						<pg-icon icon="user" />
						{{ user.name }}
					</b-nav-item>
				</b-navbar-nav>
			</div>
		</nav>

		<transition>
			<div v-if="drawerOpen" class="navbar__drawer" @click.self="toggleDrawer()">
				<b-nav vertical class="navbar__drawer-nav">
					<b-nav-item :to="{ name: 'home' }" exact>{{ $t('components.navbar.home') }}</b-nav-item>
					<b-nav-item v-if="user" :to="{ name: 'user' }" exact>
						{{ user.name }}
						<pg-icon icon="user" />
					</b-nav-item>
					<template v-if="!user">
						<b-nav-item :to="{ name: 'register' }">{{ $t('components.navbar.register') }}</b-nav-item>
						<b-nav-item :to="{ name: 'login' }">{{ $t('components.navbar.login') }}</b-nav-item>
					</template>
					<b-nav-item :to="{ name: 'promote' }">{{ $t('components.navbar.promote') }}</b-nav-item>
					<b-nav-item :to="{ name: 'about' }">{{ $t('components.navbar.company') }}</b-nav-item>
					<b-nav-item :to="{ name: 'playResponsibly' }">{{ $t('components.navbar.responsible') }}</b-nav-item>
				</b-nav>
			</div>
		</transition>
	</div>
</template>