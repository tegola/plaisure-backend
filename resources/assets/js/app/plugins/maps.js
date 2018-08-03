import { load } from 'vue2-google-maps';
import { GOOGLE_MAPS_API_KEY } from 'prontogioco/constants';

load({
	key: GOOGLE_MAPS_API_KEY,
	language: 'it', // FIXME: Use user locale
	region: 'it', // FIXME: Use user locale
	libraries: 'places'
});