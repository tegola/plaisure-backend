import { load } from 'vue2-google-maps';
import { GOOGLE_MAPS_API_KEY } from 'prontogioco/constants';

const locale = process.env.MIX_LOCALE;

load({
	key: GOOGLE_MAPS_API_KEY,
	language: locale,
	region: locale,
	libraries: 'places'
});