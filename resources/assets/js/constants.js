export const APP_NAME = 'ProntoGioco';

export const COMPANY_NAME = 'PRG s.r.l.';
export const COMPANY_VAT_NUMBER = '02554710695';

export const EMAIL_GENERIC = 'info@prontogioco.it';
export const EMAIL_VENUES = 'venues@prontogioco.it';
export const EMAIL_REPORT = 'report@prontogioco.it';

export const SEARCH_RADIUSES = [10, 20, 30, 50, 100];

// Italy's default coords
export const MAP_DEFAULT_CENTER = {
	lat: 41.909,
	lng: 12.255
};
export const MAP_DEFAULT_BOUNDS = {
	ne: {
		lat: 47.375636,
		lng: 18.710002
	},
	sw: {
		lat: 36.323503,
		lng: 6.284465
	}
};
export const MAP_DEFAULT_ZOOM = 6;

export const GOOGLE_MAPS_API_KEY = 'AIzaSyDfes2NBJiO8mSTmmTcCliqaV3vKGMD3nk';
export const GOOGLE_ANALYTICS_CODE = 'UA-78547269-1';

export default {
	APP_NAME,
	COMPANY_NAME,
	COMPANY_VAT_NUMBER,
	EMAIL_GENERIC,
	EMAIL_VENUES,
	EMAIL_REPORT,
	SEARCH_RADIUSES,
	MAP_DEFAULT_CENTER,
	MAP_DEFAULT_BOUNDS,
	MAP_DEFAULT_ZOOM,
	GOOGLE_MAPS_API_KEY,
	GOOGLE_ANALYTICS_CODE
};