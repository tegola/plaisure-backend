$(function(){
	var $form = $('.form-search');
	var whatTextbox = $form.find('[name=what]');
	var nearTextbox = $form.find('[name=near]');
	var latField = $form.find('[name=lat]');
	var lngField = $form.find('[name=lng]');
	var submitButton = $form.find('[submit]');
	var geocoder = new google.maps.Geocoder();
	var bannedComponentTypes = [];

	var formatGeocoderResult = function(result) {
		var street, city;

		$(result.address_components).each(function(index, address_component){
			$(address_component.types).each(function(index, type){
				switch (type) {
					case 'route':
						street = address_component.long_name;
						break;
					case 'administrative_area_level_3':
						city = address_component.long_name;
						break;
				}
			});
		});

		if (!street && !city) {
			return null;
		} else if (street && city) {
			return [street, city].join(', ');
		} else {
			return street || city;
		}
	};

	// What typeahead
	whatTextbox.typeahead({
		items: 5,
		delay: 200,
		separator: false, // Disable menu separators when reading data
		autoSelect: false,
		source: function(query, process){
			$.get('/venues/suggestions', $form.serializeArray()).then(process);
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

	// Latitude/longitude reset when typing
	nearTextbox.on('keydown', function(){
		latField.val('');
		lngField.val('');
	});

	// Location typeahead
	nearTextbox.typeahead({
		minLength: 2,
		items: 8,
		delay: 300,
		source: function(query, process){
			geocoder.geocode({
				address: nearTextbox.val(),
			}, function(results, status){
				if (status != google.maps.GeocoderStatus.OK) {
					return;
				}

				var items = [];

				// Add location to list only if it's not a banned type
				$(results).each(function(index, result){
					console.log(result);
					var found = false;

					$(result.address_components).each(function(index, address_component){
						if (found) return false;
						$(address_component.types).each(function(index, type){
							if (found) return false;
							if (bannedComponentTypes.indexOf(type) < 0) {
								var formattedAddress = formatGeocoderResult(result);
								if (formattedAddress) {
									items.push({
										'name': formatGeocoderResult(result), // result.formatted_address,
										'lat': result.geometry.location.lat(),
										'lng': result.geometry.location.lng()
									});
								}
								found = true;
							} else {
								//console.log('banned', type);
							}
						});
					});
				});

				// Pass locations to next step
				process(items);
			});
		},
		matcher: function(item) {
			return item;
		},
		highlighter: function(text, suggestion){ // no highlight, just a renderer
			var template = $('<div></div>');
			var nameContainer1 = $('<div class="text-truncate"></div>');
			var nameContainer2 = $('<strong></strong>').html(suggestion.name).attr('title', suggestion.name);
			nameContainer1.append(nameContainer2);
			template.append(nameContainer1);

			return template[0];
		},
		afterSelect: function(item){
			latField.val(item.lat);
			lngField.val(item.lng);
		}
	});
});