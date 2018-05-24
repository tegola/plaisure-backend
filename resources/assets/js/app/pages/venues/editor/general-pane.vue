<script>
import { geocode } from 'prontogioco/utilities/geocoder';
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

import formGroupProps from './form-group-props'

export default {
	name: 'PgVenueEditorGeneralPane',

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
		},
		defaultCoords: {
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
			}
		} else {
			mapCenter = this.defaultCoords
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
			const address = this.mutableAddress;

			if (!address.street ||
				!address.number ||
				!address.postcode ||
				!address.city ||
				!address.province
			) return;

			const addressString = [
				address.street,
				address.number,
				address.postcode,
				address.city, 
				address.province
			].join(', ');

			this.findingMarkerLocation = true;

			geocode(addressString, (error, results) => {
				this.findingMarkerLocation = false

				if (error) return;

				const lat = results[0].latitude;
				const lng = results[0].longitude;

				const coords = {
					lat: lat,
					lng: lng
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
			})
		}
	}
}
</script>

<template>
	<div>
		<b-form-group
			label="Nome"
			v-bind="formGroupProps"
			:state="!$v.name.$error"
			invalid-feedback="Inserisci il nome della tua attività.">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input v-model="venue.name" placeholder="Es.: Casinò Las Vegas" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Concessionario"
			v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-lg-9">
					<b-select v-model="venue.concessionaire_id">
						<option :value="null">Nessuno</option>
						<option v-for="item in concessionaires" :value="item.id">{{ item.name }}</option>
					</b-select>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Descrizione"
			v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-lg-9">
					<b-textarea v-model="venue.description" rows="2" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Dimensioni"
			v-bind="formGroupProps"
			:state="!$v.surface_size.$error"
			invalid-feedback="Inserisci le dimensioni.">
			<div class="form-row">
				<div class="col-5 col-md-4 col-lg-3 col-xl-2">
					<b-input-group append="mq.">
						<b-input type="number" v-model.number="venue.surface_size" min="1" />
					</b-input-group>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Categoria"
			label-class="pt-0"
			v-bind="formGroupProps"
			:state="!$v.category_ids.$error"
			invalid-feedback="Scegli almeno una categoria.">
			<div class="form-row">
				<div class="col-lg-9">
					<b-checkbox-group v-model="venue.category_ids" stacked>
						<b-checkbox v-for="category in categories" :value="category.id" :key="category.id">{{ category.name}}</b-checkbox>
					</b-checkbox-group>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Indirizzo"
			v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-9 col-lg-6">
					<b-input v-model="mutableAddress.street" placeholder="Via" />
				</div>
				<div class="col-3 col-lg-3">
					<b-input v-model="mutableAddress.number" placeholder="N. civico" />
				</div>
			</div>
		</b-form-group>
		<b-form-group
			label="CIttà"
			v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input v-model="mutableAddress.city" />
				</div>
			</div>
		</b-form-group>
		<b-form-group
			label="CAP e provincia"
			v-bind="formGroupProps"
			:state="!$v.address.$error"
			invalid-feedback="Inserisci tutti i dati dell'indirizzo.">
			<div class="form-row">
				<div class="col-3 col-lg-3">
					<b-input v-model="mutableAddress.postcode" placeholder="CAP" />
				</div>
				<div class="col-9 col-lg-6">
					<b-input v-model="mutableAddress.province" placeholder="Provincia" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Posizione esatta"
			v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-lg-9">
					<div class="embed-responsive embed-responsive-3by2 rounded">
						<pg-map class="embed-responsive-item" :center="mapCenter" :zoom="mapZoom">
							<pg-map-marker
								v-if="markerPosition"
								:position="markerPosition"
								:draggable="canDragMarker"
								@dragend="onMarkerDragEnd">
							</pg-map-marker>
						</pg-map>
					</div>
				</div>
			</div>
			<b-form-text tag="span">
				<template v-if="findingMarkerLocation">Cerco&hellip;</template>
				<template v-else-if="canDragMarker">Trascina per riposizionare</template>
			</b-form-text>
		</b-form-group>
	</div>
</template>