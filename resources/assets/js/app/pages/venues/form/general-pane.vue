<script>
import _throttle from 'lodash/throttle';

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
import { DEFAULT_COORDS } from 'prontogioco/constants';

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
		venue: {
			type: Object,
			required: true
		},
		concessionaires: {
			type: Array,
			default: () => []
		},
		categories: {
			type: Array,
			default: () => []
		},
		address: {
			type: Object,
			required: true
		},
		coords: {
			type: Object,
			required: true
		}
	},

	data() {
		const coords = this.coords;
		let mapCenter;

		if (coords.lat && coords.lng) {
			mapCenter = {
				lat: coords.lat,
				lng: coords.lng
			};
		} else {
			mapCenter = DEFAULT_COORDS;
		}

		return {
			formGroupProps,
			mutableAddress: this.address,
			mapCenter: mapCenter,
			mapZoom: coords.lat && coords.lng ? 15 : 5,
			findingMarkerLocation: false
		};
	},

	computed: {
		$v() {
			return this.$parent.$v.venue;
		},

		markerPosition() {
			const coords = this.coords;

			if (!coords.lat || !coords.lng) return null;

			return {
				lat: coords.lat,
				lng: coords.lng
			};
		},

		canDragMarker() {
			const address = this.address;

			return address.street &&
				address.number &&
				address.postcode &&
				address.city &&
				address.province &&
				!this.findingMarkerLocation ? true : false;
		}
	},

	watch: {
		mutableAddress: {
			deep: true,
			handler: function() {
				this.findMarkerLocation();
				this.$emit('update:address', this.mutableAddress);
			}
		}
	},

	methods: {
		findMarkerLocation: _throttle(function() {
			let address = this.mutableAddress;

			if (!address.street ||
				!address.number ||
				!address.postcode ||
				!address.city ||
				!address.province
			) return;

			address = [
				address.street,
				address.number,
				address.postcode,
				address.city,
				address.province
			].join(', ');

			this.findingMarkerLocation = true;

			if (!this.geocoder) this.geocoder = new google.maps.Geocoder();

			this.geocoder.geocode({ address }, (results, status) => {
				this.findingMarkerLocation = false;

				if (status != 'OK') return;

				const coords = {
					lat: results[0].geometry.location.lat(),
					lng: results[0].geometry.location.lng()
				};

				this.mapZoom = 15;
				this.mapCenter = coords;
				this.$emit('update:coords', coords);
			});
		}, 1000),

		onMarkerDragEnd(location) {
			const markerCoords = location.latLng;

			this.$emit('update:coords', {
				lat: markerCoords.lat(),
				lng: markerCoords.lng()
			});
		}
	}
};
</script>

<template>
	<div class="my-5">
		<h4>{{ $t('pages.venue_form.general.title') }}</h4>
		<hr>
		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.name.$error"
			:label="$t('pages.venue_form.general.name')"
			:invalid-feedback="$t('pages.venue_form.general.name_error')">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input v-model="venue.name" :placeholder="$t('pages.venue_form.general.name_placeholder')" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.general.concessionaire')">
			<div class="form-row">
				<div class="col-lg-9">
					<b-select v-model="venue.concessionaire_id">
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
				<div class="col-lg-9">
					<b-textarea v-model="venue.description" rows="2" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.surface_size.$error"
			:label="$t('pages.venue_form.general.surface_size')"
			:invalid-feedback="$t('pages.venue_form.general.surface_size_error')">
			<div class="form-row">
				<div class="col-5 col-md-4 col-lg-3 col-xl-2">
					<b-input-group :append="$t('pages.venue_form.general.surface_size_unit')">
						<b-input v-model.number="venue.surface_size" type="number" min="1" />
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
				<div class="col-lg-9">
					<b-checkbox-group v-model="venue.category_ids" stacked>
						<b-checkbox v-for="category in categories" :value="category.id" :key="category.id">{{ $t(`db.categories.${category.machine_name}`) }}</b-checkbox>
					</b-checkbox-group>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.general.address')">
			<div class="form-row">
				<div class="col-9 col-lg-6">
					<b-input v-model="mutableAddress.street" :placeholder="$t('pages.venue_form.general.address_placeholder1')" />
				</div>
				<div class="col-3 col-lg-3">
					<b-input v-model="mutableAddress.number" :placeholder="$t('pages.venue_form.general.address_placeholder2')" />
				</div>
			</div>
		</b-form-group>
		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.general.city')">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input v-model="mutableAddress.city" />
				</div>
			</div>
		</b-form-group>
		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.address.$error"
			:label="$t('pages.venue_form.general.zipcode_province')"
			:invalid-feedback="$t('pages.venue_form.general.address_error')">
			<div class="form-row">
				<div class="col-3 col-lg-3">
					<b-input v-model="mutableAddress.postcode" :placeholder="$t('pages.venue_form.general.zipcode_placeholder')" />
				</div>
				<div class="col-9 col-lg-6">
					<b-input v-model="mutableAddress.province" :placeholder="$t('pages.venue_form.general.province_placeholder')" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.general.location')">
			<div class="form-row">
				<div class="col-lg-9">
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
				<template v-if="findingMarkerLocation">{{ $t('pages.venue_form.general.location_searching') }}&hellip;</template>
				<template v-else-if="canDragMarker">{{ $t('pages.venue_form.general.location_hint') }}</template>
			</b-form-text>
		</b-form-group>
	</div>
</template>