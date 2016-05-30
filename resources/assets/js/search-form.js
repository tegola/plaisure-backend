$(function(){
	var $form = $('.form-search');
	var whatTextbox = $form.find('[name=what]');
	var nearTextbox = $form.find('[name=near]');

	// Search typeahead
	whatTextbox.typeahead({
		items: 5,
		delay: 200,
		separator: false, // Disable menu separators when reading data
		source: function(query, callback){
			$.get('/venues/suggestions', $form.serializeArray()).then(callback);
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

	// City typeahead
	/*
	nearTextbox.typeahead({
		minLength: 3,
		items: 5,
		delay: 200,
		separator: false,
		source: function(query, callback){
			$.get('https://maps.googleapis.com/maps/api/geocode/json', {
				address: nearTextbox.val()
			}).then(callback);
		},
		matcher: function(suggestion){ // match all results, since search happens on the server
			return true;
		}
	});
	*/

});