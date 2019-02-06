import { load } from 'vue2-google-maps';
import { GOOGLE_MAPS_API_KEY, APP_LOCALE_LANGUAGE, APP_LOCALE_REGION } from '@/constants';

load({
	key: GOOGLE_MAPS_API_KEY,
	language: APP_LOCALE_LANGUAGE,
	region: APP_LOCALE_REGION,
	libraries: 'places'
});