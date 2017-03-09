import $ from 'jquery'; // FIXME: Remove jquery dependency

const errorMsg = 'Location not found.';
const googleGeocoderUrl = 'http://maps.googleapis.com/maps/api/geocode/json';
// FIXME: Pass region per site and language per user locale
const googleGeocoderOptions = {
	key: pg.config.googleMapsApiKey,
	language: pg.config.locale,
	region: pg.config.locale
};

function geocode(address, callback) {
	if (!address) return null;

	const opts = $.extend(googleGeocoderOptions, {
		address: address
	});

	$.get(googleGeocoderUrl, opts, (data) => {
		if (data.status != 'OK' || !data.results) {
			callback(new Error(data.error_message || errorMsg));
		} else {
			const formattedResults = data.results.map(format);
			callback(null, formattedResults);
		}
	});
}

function geocodeByIp(callback) {
	$.get('https://freegeoip.net/json/').then((location) => {
		if (!location) {
			callback(new Error(errorMsg));
		}

		callback(null, location);
	});
}

function reverse(lat, lng, callback) {
	if (!lat || !lng) return null;

	const opts = $.extend(googleGeocoderOptions, {
		latlng: [lat, lng].join()
	});

	$.get(googleGeocoderUrl, opts, (data) => {
		if (data.status != 'OK' || !data.results) {
			callback(new Error(data.error_message || errorMsg));
		} else {
			callback(null, format(data.results[0]));
		}
	});
}

function format(result) {
	const googleConfidenceLookup = {
		ROOFTOP: 1,
		RANGE_INTERPOLATED: 0.9,
		GEOMETRIC_CENTER: 0.7,
		APPROXIMATE: 0.5
	};

	const extractedObj = {
		formattedAddress: result.formatted_address || null,
		latitude: result.geometry.location.lat,
		longitude: result.geometry.location.lng,
		extra: {
			googlePlaceId: result.place_id || null,
			confidence: googleConfidenceLookup[result.geometry.location_type] || 0,
			premise: null,
			subpremise: null,
			neighborhood: null,
			establishment: null
		},
		administrativeLevels: {
		}
	};

	for (let i = 0; i < result.address_components.length; i++) {
		let addressType = result.address_components[i].types[0];
		switch (addressType) {
		// Country
		case 'country':
			extractedObj.country = result.address_components[i].long_name;
			extractedObj.countryCode = result.address_components[i].short_name;
			break;
		// Administrative Level 1
		case 'administrative_area_level_1':
			extractedObj.administrativeLevels.level1long = result.address_components[i].long_name;
			extractedObj.administrativeLevels.level1short = result.address_components[i].short_name;
			break;
		// Administrative Level 2
		case 'administrative_area_level_2':
			extractedObj.administrativeLevels.level2long = result.address_components[i].long_name;
			extractedObj.administrativeLevels.level2short = result.address_components[i].short_name;
			break;
		// Administrative Level 3
		case 'administrative_area_level_3':
			extractedObj.administrativeLevels.level3long = result.address_components[i].long_name;
			extractedObj.administrativeLevels.level3short = result.address_components[i].short_name;
			break;
		// Administrative Level 4
		case 'administrative_area_level_4':
			extractedObj.administrativeLevels.level4long = result.address_components[i].long_name;
			extractedObj.administrativeLevels.level4short = result.address_components[i].short_name;
			break;
		// Administrative Level 5
		case 'administrative_area_level_5':
			extractedObj.administrativeLevels.level5long = result.address_components[i].long_name;
			extractedObj.administrativeLevels.level5short = result.address_components[i].short_name;
			break;
		// City
		case 'locality':
			extractedObj.city = result.address_components[i].long_name;
			break;
		// Address
		case 'postal_code':
			extractedObj.zipcode = result.address_components[i].long_name;
			break;
		case 'route':
			extractedObj.streetName = result.address_components[i].long_name;
			break;
		case 'street_number':
			extractedObj.streetNumber = result.address_components[i].long_name;
			break;
		case 'premise':
			extractedObj.extra.premise = result.address_components[i].long_name;
			break;
		case 'subpremise':
			extractedObj.extra.subpremise = result.address_components[i].long_name;
			break;
		case 'establishment':
			extractedObj.extra.establishment = result.address_components[i].long_name;
			break;
		case 'sublocality_level_1':
		case 'political':
		case 'sublocality':
		case 'neighborhood':
			if (!extractedObj.extra.neighborhood) {
				extractedObj.extra.neighborhood = result.address_components[i].long_name;
			}
			break;
		}
	}

	return extractedObj;
}

export { geocode, geocodeByIp, reverse };