$(function(){
	var $form = $('.form-search');
	var whatTextbox = $form.find('[name=what]');
	var nearTextbox = $form.find('[name=near]');
	var latField = $form.find('[name=lat]');
	var lngField = $form.find('[name=lng]');

	// What typeahead
	whatTextbox.typeahead({
		items: 5,
		delay: 200,
		separator: false, // Disable menu separators when reading data
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

	// Location typeahead
	nearTextbox.typeahead({
		minLength: 2,
		items: 8,
		delay: 200,
		source: function(query, process){
			$.get('https://maps.googleapis.com/maps/api/geocode/json', {
				address: nearTextbox.val(),
				language: 'it'
			}).then(function(data){
				var locations = [];

				$(data.results).each(function(index, result){
					// Add location to list only if it's a city
					$(result.address_components).each(function(index, address_component){
						if ($.inArray('administrative_area_level_3', address_component.types) !== -1) {
							locations.push({
								'name': address_component.long_name,
								'lat': result.geometry.location.lat,
								'lng': result.geometry.location.lng
							});
							return false;
						}
					});
				});

				// Pass locations to next step
				process(locations);
			});
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