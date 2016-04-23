/* global google */
/*
jQuery.extend({
	getQueryParams: function(str) {
		return (str || document.location.search).replace(/(^\?)/,'').split("&").map(function(n){return n = n.split("="),this[n[0]] = n[1],this}.bind({}))[0];
	}
});
*/

function findCityNameInResults(results){
	var name;

	$(results).each(function(index, result){ // Read results
		// Stop if city has been found already
		if (name) {
			return false;
		}

		$(result.address_components).each(function(index, address_component){ // Read address components
			// Stop if city has been found already
			if (name) {
				return false;
			}

			if ($.inArray('administrative_area_level_3', address_component.types) !== -1) {
				name = address_component.long_name;
				return false;
			}
		});
	});

	return name;
}

// HOME -----------------------------------------------------------------------
$(function(){
	// Stop if page was not found
	if (!$('.page-home').length) {
		return;
	}

	var $mapNode = $('.map');

	// Init Google Maps map, if visible (it's not visible on small screens)
	if ($mapNode.height()) {
		var geocoder = new google.maps.Geocoder();

		// Create the static map with no labels
		var map = new google.maps.Map($mapNode[0], {
			disableDefaultUI: true,
			scrollwheel: false,
			draggable: false,
			disableDoubleClickZoom: true
		});
		map.setOptions({
			styles: [
				{ // Remove labels
					"stylers": [{ "saturation": -100 }, { "gamma": 0.5 }]
				},
				{ // Remove color
					"elementType": "labels",
					"stylers": [{ "visibility": "off" }]
				},
				{ // Less visible highways
					"featureType": "road.highway",
					"stylers": [{ "lightness": 50 }]
				},
				{ // Thinner roads
					"featureType": "road",
					"elementType": "geometry.stroke",
					"stylers": [{ "weight": 0.3 }]
				}
			]
		});


		// Locate user, then center map and set city name inside the search field
		navigator.geolocation.getCurrentPosition(
			function(position) {
				var lat = position.coords.latitude;
				var lng = position.coords.longitude;
				var coords = new google.maps.LatLng(lat, lng);

				// Recenter and zoom map
				map.setCenter(coords);
				map.setZoom(15);

				// Fill lat and lng hidden fields
				$('[name=lat]').val(lat);
				$('[name=lng]').val(lng);

				// Fill "near" textbox with current city
				geocoder.geocode({ 'location': coords }, function(results, status){
					if (status !== google.maps.GeocoderStatus.OK) {
						return;
					}

					$('[name=near]').val(findCityNameInResults(results));
				});
			},
			function(){
				var geocoder = new google.maps.Geocoder();

				geocoder.geocode({ 'address': 'Italy' }, function(results, status){
					if (status !== google.maps.GeocoderStatus.OK) {
						return;
					}

					var coords = results[0].geometry.location;

					map.setCenter(coords);
					map.setZoom(5);
				});
			}
		);
	}
});

// EXPLORE --------------------------------------------------------------------
$(function(){
	// Stop if page was not found
	if (!$('.page-explore').length) {
		return;
	}

	var lat = $('[name=lat]').val();
	var lng = $('[name=lng]').val();
	var coords = new google.maps.LatLng(lat, lng);
	var $mapNode = $('.map');
	var map = new google.maps.Map($mapNode[0], {
		center: coords,
		zoom: 15
	});
});