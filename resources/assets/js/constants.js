export const APP_NAME = process.env.MIX_APP_NAME;
export const APP_URL = process.env.MIX_APP_URL;
export const APP_HOST = new URL(APP_URL).hostname.replace('www.', ''); // http://www.url.com -> url.com

export const APP_LOCALE = process.env.MIX_APP_LOCALE;
export const APP_LOCALE_LANGUAGE = APP_LOCALE.split(/-|_/)[0];
export const APP_LOCALE_REGION = APP_LOCALE.split(/-|_/)[1];

export const COMPANY_NAME = 'PRG s.r.l.';
export const COMPANY_VAT_NUMBER = '02554710695';

export const EMAIL_GENERIC = `info@${APP_HOST}`;
export const EMAIL_VENUES = `venues@${APP_HOST}`;
export const EMAIL_REPORT = `report@${APP_HOST}`;

export const GOOGLE_MAPS_API_KEY = process.env.MIX_GOOGLE_MAPS_API_KEY;
export const GOOGLE_ANALYTICS_CODE = process.env.GOOGLE_ANALYTICS_CODE;

export const STRIPE_KEY = process.env.MIX_STRIPE_KEY;
export const STRIPE_SECRET = process.env.MIX_STRIPE_SECRET;

export const SEARCH_RADIUSES = [10, 20, 30, 50, 100];
export let SEARCH_CITIES;

export let MAP_DEFAULT_CENTER;
export let MAP_DEFAULT_BOUNDS;
export let MAP_DEFAULT_ZOOM;

switch (APP_LOCALE_REGION) {
	case 'IT':
		SEARCH_CITIES = [
			{ query: 'Milano', c_lat: 45.462734, c_lng: 9.177732, zoom: 12 },
			{ query: 'Venezia', c_lat: 45.428276, c_lng: 12.341314, zoom: 12 },
			{ query: 'Bologna', c_lat: 44.499118, c_lng: 11.331685, zoom: 12 },
			{ query: 'Roma', c_lat: 41.897570, c_lng: 12.494799, zoom: 11 },
			{ query: 'Napoli', c_lat: 40.842514, c_lng: 14.260305, zoom: 12 },
			{ query: 'Palermo', c_lat: 38.109908, c_lng: 13.362782, zoom: 12 }
		];
		MAP_DEFAULT_CENTER = { lat: 41.909, lng: 12.255 };
		MAP_DEFAULT_BOUNDS = {
			ne: { lat: 47.375636, lng: 18.710002 },
			sw: { lat: 36.323503, lng: -3.435973 }
		};
		MAP_DEFAULT_ZOOM = 6;
		break;

	case 'GB':
		SEARCH_CITIES = [
			{ query: 'London', c_lat: 51.5283064, c_lng: -0.3824554, zoom: 10 },
			{ query: 'Birmingham', c_lat: 52.4773545, c_lng: -2.2936735, zoom: 11 },
			{ query: 'Manchester', c_lat: 53.4722249, c_lng: 11.331685, zoom: 12 },
			{ query: 'Liverpool', c_lat: 53.412095, c_lng: -3.0564823, zoom: 11 },
			{ query: 'Glasgow', c_lat: 55.8553803, c_lng: -4.3728819, zoom: 11 }
		];
		MAP_DEFAULT_CENTER = { lat: 55.378051, lng: 12.255 };
		MAP_DEFAULT_BOUNDS = {
			ne: { lat: 60.915699, lng: 33.916554 },
			sw: { lat: 34.5614, lng: -8.8988999 }
		};
		MAP_DEFAULT_ZOOM = 5;
		break;
}

export default {
	APP_NAME,
	APP_URL,
	APP_HOST,
	APP_LOCALE,
	APP_LOCALE_LANGUAGE,
	APP_LOCALE_REGION,
	COMPANY_NAME,
	COMPANY_VAT_NUMBER,
	EMAIL_GENERIC,
	EMAIL_VENUES,
	EMAIL_REPORT,
	SEARCH_RADIUSES,
	SEARCH_CITIES,
	MAP_DEFAULT_CENTER,
	MAP_DEFAULT_BOUNDS,
	MAP_DEFAULT_ZOOM,
	GOOGLE_MAPS_API_KEY,
	GOOGLE_ANALYTICS_CODE,
	STRIPE_KEY,
	STRIPE_SECRET
};