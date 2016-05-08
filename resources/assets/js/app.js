/* global google */

// Support for showing geolocation controls
$('html').addClass(navigator.geolocation ? 'has-geolocation' : 'no-geolocation');


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
			console.log(address_component);

			if ($.inArray('administrative_area_level_3', address_component.types) !== -1) {
				name = address_component.long_name;
				return false;
			}
		});
	});

	return name;
}

// ALL PAGES ------------------------------------------------------------------
$(function(){
	$('[data-toggle="tooltip"]').tooltip();
});

// HOME -----------------------------------------------------------------------
$(function(){
	// Stop if page was not found
	if (!$('.page-home').length) {
		return;
	}

	var $mapNode = $('.map');
	var mapIsVisible = $mapNode.height();
	var geocoder = new google.maps.Geocoder();

	// Bind locate button to find precise location
	$('[data-action=locate]').on('click', function(e){
		e.preventDefault();

		navigator.geolocation.getCurrentPosition(
			function(position){
				var lat = position.coords.latitude;
				var lng = position.coords.longitude;
				var coords = new google.maps.LatLng(lat, lng);

				// Recenter and zoom map
				if (mapIsVisible) {
					map.setCenter(coords);
					map.setZoom(15);
				}

				// Fill lat and lng hidden fields
				$('[name=lat]').val(lat);
				$('[name=lng]').val(lng);

				// Find city name and use it to fill the City field
				geocoder.geocode({ 'location': coords }, function(results, status){
					if (status !== google.maps.GeocoderStatus.OK) {
						return;
					}

					$('[name=near]').val(findCityNameInResults(results));
				});
			},
			function(){
				alert('Non è stato possibile trovare la tua posizione.');
			},
			{
				timeout: 10 * 1000, // 10 secs
				maximumAge: 5 * 60 * 1000 // last 5 minutes
			}
		);
	});

	// Find generic location using IP info
	// or simply use the Italy position
	$.get('http://ip-api.com/json').then(function(location, status){
		if (status === 'success') {
			var coords = new google.maps.LatLng(location.lat, location.lon);

			// Recenter and zoom map
			if (mapIsVisible) {
				map.setCenter(coords);
				map.setZoom(15);
			}

			// Fill lat and lng hidden fields
			$('[name=lat]').val(location.lat);
			$('[name=lng]').val(location.lon);

			// Fill the city field
			$('[name=near]').val(location.city);
		} else {
			if (mapIsVisible) {
				geocoder.geocode({ 'address': 'Italy' }, function(results, status){
					if (status !== google.maps.GeocoderStatus.OK) {
						return;
					}

					map.setCenter(results[0].geometry.location);
					map.setZoom(5);
				});
			}
		}
	});

	// Init Google Maps map, if visible (it's not on small screens)
	if (mapIsVisible) {
		// Create the static map with no labels
		var map = new google.maps.Map($mapNode[0], {
			disableDefaultUI: true,
			scrollwheel: false,
			draggable: false,
			disableDoubleClickZoom: true
		});
		map.setOptions({
			styles: [
				{ // Remove color
					"stylers": [{ "saturation": -100 }, { "gamma": 0.5 }]
				},
				{ // Remove labels
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