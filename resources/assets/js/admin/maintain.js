import $ from 'jquery';
import Vue from 'vue';
//import * as VueGoogleMaps from 'vue2-google-maps';
//import * as geocoder from '../utilities/geocoder';

new Vue({
	el: '#app',
	data: {
		prova: 'ciao'
	}
});

$(function(){
	var streetField    = $('[name="address_street"]');
	var numberField    = $('[name="address_number"]');
	var postcodeField  = $('[name="address_postcode"]');
	var cityField      = $('[name="address_city"]');
	var provinceField  = $('[name="address_province"]');
	var regionField    = $('[name="address_region"]');
	var countryField   = $('[name="address_country"]');
	var latitudeField  = $('[name="geo_latitude"]');
	var longitudeField = $('[name="geo_longitude"]');

	// Build the map
	var hasCoords = latitudeField.val() && longitudeField.val();
	var coords = {
		lat: hasCoords ? latitudeField.val() : 42.0568112,
		lng: hasCoords ? longitudeField.val() : 11.9868858
	};
	coords = new google.maps.LatLng(coords.lat, coords.lng);
	var map = new google.maps.Map($('.map')[0], {
		center: coords,
		zoom: hasCoords ? 15 : 5
	});
	var marker = new google.maps.Marker({
		map: map,
		draggable: true
	});
	if (hasCoords) {
		marker.setPosition(coords);
	}

	// Update latitude/longitude when moving marker
	google.maps.event.addListener(marker, 'drag', function(markerData){
		latitudeField.val(markerData.latLng.lat().toFixed(6));
		longitudeField.val(markerData.latLng.lng().toFixed(6));
	});

	$('[data-toggle="geocode"]').on('click', function(e){
		e.preventDefault();

		var url = "http://maps.googleapis.com/maps/api/geocode/json";
		var params = { address: $(this).data('address').trim() };

		// Ask Google maps for the location
		$.get(url, params, function(response){
			if (response.status != 'OK') {
				alert("Non è stato possibile utilizzare Google Maps per trovare la posizione dell'attività");
				return;
			}
			
			// Prepare new values
			var result = response.results[0];
			var location = {
				latitude: result.geometry.location.lat.toFixed(6),
				longitude: result.geometry.location.lng.toFixed(6),
				street: '',
				number: '',
				city: '',
				postcode: '',
				province: '',
				region: '',
				country: ''
			};

			result.address_components.forEach(function(component){
				var t = component.types;

				if (t.indexOf('route') != -1) {
					location.street = component.long_name;
				} else if (t.indexOf('street_number') != -1) {
					location.number = component.long_name;
				} else if (t.indexOf('postal_code') != -1) {
					location.postcode = component.long_name;
				} else if (t.indexOf('locality') != -1 || t.indexOf('administrative_area_level_3') != -1) {
					location.city = component.long_name;
				} else if (t.indexOf('administrative_area_level_2') != -1) {
					location.province = component.short_name;
				} else if (t.indexOf('administrative_area_level_1') != -1) {
					location.region = component.long_name;
				} else if (t.indexOf('country') != -1) {
					location.country = component.long_name;
				}
			});

			// Fill fields with new values and focus them to lose the placeholder
			streetField.val(location.street);
			numberField.val(location.number);
			postcodeField.val(location.postcode);
			cityField.val(location.city);
			provinceField.val(location.province);
			regionField.val(location.region);
			countryField.val(location.country);
			latitudeField.val(location.latitude);
			longitudeField.val(location.longitude);

			// Update the map
			coords = new google.maps.LatLng(location.latitude, location.longitude);
			map.setCenter(coords);
			map.setZoom(15);
			marker.setPosition(coords);
		});
	});
});