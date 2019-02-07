import axios from 'axios';
import extend from 'lodash/extend';

import { GOOGLE_MAPS_API_KEY } from 'constants';

const errorMsg = 'Location not found.';
const googleGeocoderUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
// FIXME: Pass region per site and language per user locale
// Better: create a class and throw those params in a constructor
const googleGeocoderOptions = {
	key: GOOGLE_MAPS_API_KEY,
	language: 'it', // FIXME: Use user locale
	region: 'it' // FIXME: Use user locale
};

function geocode(address, callback) {
	if (!address) return null;

	axios.get(googleGeocoderUrl, {
		params: extend(googleGeocoderOptions, {
			address: address
		})
	}).then(response => {
		const data = response.data;

		if (data.status != 'OK' || !data.results) {
			callback(new Error(data.error_message || errorMsg));
		} else {
			const formattedResults = data.results.map(formatResult);
			callback(null, formattedResults);
		}
	});
}

function geocodeByIp(callback) {
	axios.get('https://freegeoip.net/json/').then(response => {
		const location = response.data;

		if (!location) callback(new Error(errorMsg));
		callback(null, location);
	});
}

function reverse(lat, lng, callback) {
	if (!lat || !lng) return null;

	axios.get(googleGeocoderUrl, {
		params: extend(googleGeocoderOptions, {
			latlng: [lat, lng].join()
		})
	}).then(response => {
		const data = response.data;

		if (data.status != 'OK' || !data.results) {
			callback(new Error(data.error_message || errorMsg));
		} else {
			callback(null, formatResult(data.results[0]));
		}
	});
}

function formatResult(result) {
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
		let components = result.address_components[i];
		let type = components.types[0];

		switch (type) {
			// Country
			case 'country':
				extractedObj.country = components.long_name;
				extractedObj.countryCode = components.short_name;
				break;
			// Administrative Level 1
			case 'administrative_area_level_1':
				extractedObj.administrativeLevels.level1long = components.long_name;
				extractedObj.administrativeLevels.level1short = components.short_name;
				break;
			// Administrative Level 2
			case 'administrative_area_level_2':
				extractedObj.administrativeLevels.level2long = components.long_name;
				extractedObj.administrativeLevels.level2short = components.short_name;
				break;
			// Administrative Level 3
			case 'administrative_area_level_3':
				extractedObj.administrativeLevels.level3long = components.long_name;
				extractedObj.administrativeLevels.level3short = components.short_name;
				break;
			// Administrative Level 4
			case 'administrative_area_level_4':
				extractedObj.administrativeLevels.level4long = components.long_name;
				extractedObj.administrativeLevels.level4short = components.short_name;
				break;
			// Administrative Level 5
			case 'administrative_area_level_5':
				extractedObj.administrativeLevels.level5long = components.long_name;
				extractedObj.administrativeLevels.level5short = components.short_name;
				break;
			// City
			case 'locality':
				extractedObj.city = components.long_name;
				break;
			// Address
			case 'postal_code':
				extractedObj.zipcode = components.long_name;
				break;
			case 'route':
				extractedObj.streetName = components.long_name;
				break;
			case 'street_number':
				extractedObj.streetNumber = components.long_name;
				break;
			case 'premise':
				extractedObj.extra.premise = components.long_name;
				break;
			case 'subpremise':
				extractedObj.extra.subpremise = components.long_name;
				break;
			case 'establishment':
				extractedObj.extra.establishment = components.long_name;
				break;
			case 'sublocality_level_1':
			case 'political':
			case 'sublocality':
			case 'neighborhood':
				if (!extractedObj.extra.neighborhood) {
					extractedObj.extra.neighborhood = components.long_name;
				}
				break;
		}
	}

	return extractedObj;
}

export {
	geocode,
	geocodeByIp,
	reverse,
	formatResult
};