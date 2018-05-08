<script>
import { geocode } from 'prontogioco/utilities/geocoder';
import _debounce from 'lodash/debounce';
import _cloneDeep from 'lodash/extend';

import BInput from 'bootstrap-vue/es/components/form-input/form-input';

import { Map as PgMap, Marker as PgMapMarker, InfoWindow as PgMapInfoWindow } from 'vue2-google-maps';

export default {
	name: 'PgVenueEditorAddressPane',

	components: {
		BInput,
		PgMap,
		PgMapMarker,
		PgMapInfoWindow
	},

	props: {
		address: {
			type: Object,
			required: true
		},
		coords: {
			type: Object,
			required: true
		},
		defaultMapCenter: {
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
			mapCenter = this.defaultMapCenter
		}

		return {
			mutableAddress: this.address,
			mapCenter: mapCenter,
			mapZoom: coords.lat && coords.lng ? 15 : 5,
			findingMarkerLocation: false,
			draggingMarker: false
		};
	},

	watch: {
		mutableAddress: {
			deep: true,
			handler: function() {
				this.findMarkerLocation();
				this.$emit('update:address', this.mutableAddress);
			}
		},
	},

	computed: {
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
				address.province ? true : false;
		}
	},

	methods: {
		findMarkerLocation: _debounce(function() {
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

		onMarkerDragStart() {
			this.draggingMarker = true;
		},

		onMarkerDrag(location) {
			const markerCoords = location.latLng;

			this.$emit('update:coords', {
				lat: markerCoords.lat(),
				lng: markerCoords.lng()
			})
		},

		onMarkerDragEnd() {
			this.draggingMarker = false;
		}
	}
}
</script>

<template>
	<div>
		<h4>Indirizzo</h4>
		<hr>
		<div class="row">
			<div class="col-lg-7">
				<div class="row">
					<div class="form-group col-8">
						<label>Via</label>
						<b-input v-model="mutableAddress.street" />
					</div>
					<div class="form-group col-4">
						<label>N. civico</label>
						<b-input v-model="mutableAddress.number" />
					</div>
					<div class="form-group col-12">
						<label>Città</label>
						<b-input v-model="mutableAddress.city" />
					</div>
					<div class="form-group col-4">
						<label>CAP</label>
						<b-input v-model="mutableAddress.postcode" />
					</div>
					<div class="form-group col-8">
						<label>Provincia</label>
						<b-input v-model="mutableAddress.province" />
					</div>
				</div>
			</div>
			<div class="col-lg-5">
				<div class="form-group">
					<label>
						<template v-if="findingMarkerLocation">
							<pg-icon icon="circle-outline-notch" spinning />
							Cerco posizione esatta
						</template>
						<template v-else>
							Posizione esatta <template v-if="canDragMarker">(trascina per riposizionare)</template>
						</template>
					</label>
					<div class="embed-responsive embed-responsive-1by1" style="height: 382px; border-radius: 5px">
						<pg-map class="embed-responsive-item" :center="mapCenter" :zoom="mapZoom">
							<pg-map-marker
								v-if="markerPosition"
								:position="markerPosition"
								:draggable="canDragMarker"
								@dragstart="onMarkerDragStart"
								@drag="onMarkerDrag"
								@dragend="onMarkerDragEnd">
								<pg-map-info-window :opened="draggingMarker">
									<strong>Latitudine:</strong> {{ coords.lat}}<br>
									<strong>Longitudine</strong> {{ coords.lng }}
								</pg-map-info-window>
							</pg-map-marker>
						</pg-map>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>