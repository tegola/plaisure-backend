<script>
import _throttle from 'lodash/throttle';
import _extend from 'lodash/extend';
import _isEqual from 'lodash/isEqual';

import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BFormText from 'bootstrap-vue/es/components/form/form-text';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BInputGroup from 'bootstrap-vue/es/components/input-group/input-group';
import BTextarea from 'bootstrap-vue/es/components/form-textarea/form-textarea';
import BSelect from 'bootstrap-vue/es/components/form-select/form-select';
import BCheckbox from 'bootstrap-vue/es/components/form-checkbox/form-checkbox';
import BCheckboxGroup from 'bootstrap-vue/es/components/form-checkbox/form-checkbox-group';
import {
	Map as PgMap,
	Marker as PgMapMarker,
	InfoWindow as PgMapInfoWindow
} from 'vue2-google-maps';
import formGroupProps from './form-group-props';
import { MAP_DEFAULT_CENTER } from 'prontogioco/constants';

export default {
	name: 'PgVenueFormGeneralPane',

	components: {
		BFormGroup,
		BFormText,
		BInput,
		BInputGroup,
		BTextarea,
		BSelect,
		BCheckbox,
		BCheckboxGroup,
		PgMap,
		PgMapMarker,
		PgMapInfoWindow
	},

	props: {
		venueId: {
			type: [String, Number],
			default: null
		}
	},

	data() {
		return {
			formGroupProps,
			mapCenter: MAP_DEFAULT_CENTER,
			mapZoom: 5,
			searchingMarkerCoords: false
		};
	},

	computed: {
		storeName() {
			return `venueForm/${this.venueId || 'new'}`;
		},

		venue() {
			return this.$store.state[this.storeName].venue;
		},

		concessionaires() {
			return this.$store.state[this.storeName].concessionaires;
		},

		categories() {
			return this.$store.state[this.storeName].categories;
		},

		venueName: {
			get() {
				return this.venue.name;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'name',
					value
				});
			}
		},

		venueConcessionaireId: {
			get() {
				return this.venue.concessionaire_id;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'concessionaire_id',
					value
				});
			}
		},

		venueDescription: {
			get() {
				return this.venue.description;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'description',
					value
				});
			}
		},

		venueSurfaceSize: {
			get() {
				return this.venue.surface_size;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'surface_size',
					value
				});
			}
		},

		venueCategoryIds: {
			get() {
				return this.venue.category_ids;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'category_ids',
					value
				});
			}
		},

		venueAddress: {
			get() {
				return this.venue.address;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'address',
					value
				});
			}
		},

		venueCoords: {
			get() {
				return this.venue.coords;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'coords',
					value
				});
			}
		},

		$v() {
			return this.$parent.$v.venue;
		},

		markerPosition() {
			const coords = this.venueCoords;

			if (!coords.lat || !coords.lng) return null;

			return {
				lat: coords.lat,
				lng: coords.lng
			};
		},

		showMap() {
			const a = this.venueAddress;
			return Boolean(a.line1 && a.postcode && a.city && a.province);
		},

		canDragMarker() {
			return this.showMap && !this.searchingMarkerCoords;
		}
	},

	watch: {
		venueCoords: {
			immediate: true,
			handler() {
				this.mapCenter = this.venueCoords;
				this.markerCoords = this.venueCoords;
				if (!_isEqual(this.venueCoords, MAP_DEFAULT_CENTER)) this.mapZoom = 15;
			}
		}
	},

	methods: {
		onAddressInput (field, value) {
			// Save new address
			const address = _extend({}, this.venueAddress, {
				[field]: value
			});

			this.venueAddress = address;

			// Find marker location
			this.findMarkerCoords();
		},

		findMarkerCoords: _throttle(function() {
			let address = this.venueAddress;

			if (!address.line1 ||
				!address.postcode ||
				!address.city ||
				!address.province
			) return;

			address = [
				address.line1,
				address.line2,
				address.postcode,
				address.city,
				address.province
			].join(', ');

			this.searchingMarkerCoords = true;

			if (!this.geocoder) this.geocoder = new google.maps.Geocoder();

			this.geocoder.geocode({ address }, (results, status) => {
				this.searchingMarkerCoords = false;

				if (status != 'OK') return;

				const location = results[0].geometry.location;
				const coords = {
					lat: location.lat(),
					lng: location.lng()
				};

				this.mapZoom = 15;
				this.mapCenter = coords;
				this.venueCoords = coords;
			});
		}, 1000),

		onMarkerDragEnd(location) {
			const markerCoords = location.latLng;

			this.venueCoords = {
				lat: markerCoords.lat(),
				lng: markerCoords.lng()
			};
		}
	}
};
</script>

<template>
	<div class="my-5">
		<h5>{{ $t('pages.venue_form.general.title') }}</h5>
		<hr>
		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.name.$error"
			:label="$t('pages.venue_form.general.name')"
			:invalid-feedback="$t('pages.venue_form.general.name_error')">
			<div class="form-row">
				<div class="col-lg-10">
					<b-input v-model="venueName" :placeholder="$t('pages.venue_form.general.name_placeholder')" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.general.concessionaire')">
			<div class="form-row">
				<div class="col-lg-10">
					<b-select v-model="venueConcessionaireId">
						<option :value="null">{{ $t('pages.venue_form.general.concessionaire_none') }}</option>
						<option v-for="item in concessionaires" :value="item.id" :key="item.id">{{ item.name }}</option>
					</b-select>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.general.description')">
			<div class="form-row">
				<div class="col-lg-10">
					<b-textarea v-model="venueDescription" rows="2" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.surface_size.$error"
			:label="$t('pages.venue_form.general.surface_size')"
			:invalid-feedback="$t('pages.venue_form.general.surface_size_error')">
			<div class="form-row">
				<div class="col-5 col-md-4 col-lg-3">
					<b-input-group :append="$t('pages.venue_form.general.surface_size_unit')">
						<b-input v-model.number="venueSurfaceSize" type="number" min="1" />
					</b-input-group>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.category_ids.$error"
			:label="$t('pages.venue_form.general.category')"
			:invalid-feedback="$t('pages.venue_form.general.category_error')"
			label-class="pt-0">
			<div class="form-row">
				<div class="col-lg-10">
					<b-checkbox-group v-model="venueCategoryIds" stacked>
						<b-checkbox v-for="category in categories" :value="category.id" :key="category.id">{{ $t(`db.categories.${category.machine_name}`) }}</b-checkbox>
					</b-checkbox-group>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.general.address')">
			<div class="form-row">
				<div class="col-lg-10 mb-2">
					<b-input :placeholder="$t('pages.venue_form.general.address_line1_placeholder')" :value="venueAddress.line1" @input="onAddressInput('line1', $event)" />
				</div>
				<div class="col-lg-10">
					<b-input :placeholder="$t('pages.venue_form.general.address_line2_placeholder')" :value="venueAddress.line2" @input="onAddressInput('line2', $event)" />
				</div>
			</div>
		</b-form-group>
		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.general.city')">
			<div class="form-row">
				<div class="col-lg-10">
					<b-input :value="venueAddress.city" @input="onAddressInput('city', $event)" />
				</div>
			</div>
		</b-form-group>
		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.address.$error"
			:label="$t('pages.venue_form.general.zipcode_province')"
			:invalid-feedback="$t('pages.venue_form.general.address_error')">
			<div class="form-row">
				<div class="col-3">
					<b-input :placeholder="$t('pages.venue_form.general.zipcode_placeholder')" :value="venueAddress.postcode" @input="onAddressInput('postcode', $event)" />
				</div>
				<div class="col-9 col-lg-7">
					<b-input :placeholder="$t('pages.venue_form.general.province_placeholder')" :value="venueAddress.province" @input="onAddressInput('province', $event)" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-if="showMap"
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.general.location')">
			<div class="form-row">
				<div class="col-lg-10">
					<div class="embed-responsive embed-responsive-3by2 rounded">
						<pg-map :center="mapCenter" :zoom="mapZoom" class="embed-responsive-item">
							<pg-map-marker
								v-if="markerPosition"
								:position="markerPosition"
								:draggable="canDragMarker"
								@dragend="onMarkerDragEnd"
							/>
						</pg-map>
					</div>
				</div>
			</div>
			<b-form-text tag="span">
				<template v-if="searchingMarkerCoords">{{ $t('pages.venue_form.general.location_searching') }}&hellip;</template>
				<template v-else-if="canDragMarker">{{ $t('pages.venue_form.general.location_hint') }}</template>
			</b-form-text>
		</b-form-group>
	</div>
</template>