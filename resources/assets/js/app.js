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

	// Setup search form (home and navbar)
	$('.form-search').each(function(index, form){
		$form = $(form);

		// Setup typeahead
		$form.find('[name=what]').typeahead({
			items: 5,
			delay: 200,
			separator: false, // Disable menu separators when reading data
			source: function(query, cb){
				$.get('/venues/suggestions', $form.serializeArray()).then(cb);
			},
			matcher: function(suggestion){ // match all results, since search happens on the server
				return true;
			},
			highlighter: function(text, suggestion){ // no highlight, just a renderer
				var template = $('<div></div>');

				// Name
				var nameContainer1 = $('<div class="text-truncate"></div>');
				var nameContainer2 = $('<strong></strong>').html(suggestion.name).attr('title', suggestion.name);
				nameContainer1.append(nameContainer2);
				template.append(nameContainer1);

				// Category and city
				if (suggestion.type == 'venue') {
					var metaText = [suggestion.category, suggestion.city].join(', ');
					var metaContainer = $('<div class="text-muted text-truncate">').html(metaText).attr('title', metaText);
					template.append(metaContainer);
				}

				return template[0];
			},
			afterSelect: function(item){
				// Go to venue page on select
				if (item.type == 'venue' && item.id) {
					location.href = '/venues/' + item.id;
				}
			}
		});

		// Setup submit
		/*
		$form.on('submit', function(){
			var values = $form.serializeArray();
			console.log(values);
			return false;
		});
		*/
	});
});

// HOME -----------------------------------------------------------------------
$(function(){
	// Stop if page was not found
	if (!$('.page-home').length) {
		return;
	}

	var $mapNode = $('.map');
	var form = $('.search-card-block');
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