import { load } from 'vue2-google-maps';
import { GOOGLE_MAPS_API_KEY, APP_LOCALE } from 'prontogioco/constants';

load({
	key: GOOGLE_MAPS_API_KEY,
	language: APP_LOCALE,
	region: APP_LOCALE,
	libraries: 'places'
});