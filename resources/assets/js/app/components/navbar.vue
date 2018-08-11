<script>
import { mapState } from 'vuex';
import _extend from 'lodash/extend';

import BNavbarNav from 'bootstrap-vue/es/components/navbar/navbar-nav';
import BNavItemDropdown from 'bootstrap-vue/es/components/nav/nav-item-dropdown';
import BDropdownItem from 'bootstrap-vue/es/components/dropdown/dropdown-item';
import BDropdownItemButton from 'bootstrap-vue/es/components/dropdown/dropdown-item-button';
import BDropdownDivider from 'bootstrap-vue/es/components/dropdown/dropdown-divider';

import PgLogo from './logo';
import PgIcon from './icon';
import PgPlaceTextbox from './place-textbox';

import constants from 'prontogioco/constants';

export default {
	name: 'PgNavbar',

	components: {
		BNavbarNav,
		BNavItemDropdown,
		BDropdownItem,
		BDropdownItemButton,
		BDropdownDivider,
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
		...mapState('user', ['user', 'venues']),
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
		},

		logout() {
			this.$store.dispatch('user/logout')
				.then(() => {
					this.$router.push({ name: 'home' });
				});
		}
	}
};
</script>

<template>
	<nav :class="classes" class="navbar navbar-expand">
		<div :class="fluid ? 'container-fluid' : 'container'">
			<router-link :aria-label="APP_NAME" class="navbar-brand" to="/">
				<pg-logo :text="showLogoText" :class="['navbar__logo', !showLogoText ? 'navbar__logo--no-text' : '']" />
			</router-link>

			<div class="navbar__divider" />

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
					<b-nav-item-dropdown right>
						<template slot="button-content">
							<pg-icon icon="user" />
							{{ user.name }}
						</template>
						<b-dropdown-item :to="{ name: 'user.venues' }" active-class="" exact-active-class="">{{ venues.length ? $t('components.navbar.dropdown.venues') : $t('components.navbar.dropdown.add') }}</b-dropdown-item>
						<b-dropdown-item :to="{ name: 'user.edit' }" active-class="" exact-active-class="">{{ $t('components.navbar.dropdown.edit') }}</b-dropdown-item>
						<b-dropdown-divider />
						<b-dropdown-item-button @click="logout">{{ $t('components.navbar.dropdown.logout') }}</b-dropdown-item-button>
					</b-nav-item-dropdown>
				</b-navbar-nav>
			</div>
		</div>
	</nav>
</template>