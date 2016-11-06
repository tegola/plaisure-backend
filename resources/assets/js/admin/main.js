function geocode(street, cityAndProvince) {
	var url = "http://maps.googleapis.com/maps/api/geocode/json";
	var params = {
		address: [street, cityAndProvince].join(' ').trim()
	};

	$.get(url, params, function(){
		console.log('success');
	});
}