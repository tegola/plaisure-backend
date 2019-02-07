import i18n from '@/lang';
import { APP_LOCALE_LANGUAGE, APP_LOCALE_REGION } from '@/constants';

// Load all supported languages, we can't load them dynamically in a build
import countriesIt from '@umpirsky/country-list/data/it/country.json';
import countriesEn from '@umpirsky/country-list/data/en/country.json';

let countries;
const options = [];

switch (APP_LOCALE_LANGUAGE) {
	case 'it': countries = countriesIt; break;
	case 'en': countries = countriesEn; break;
}

Object.keys(countries).forEach(code => {
	const obj = {
		value: code,
		text: countries[code]
	};

	// Add current country + separator to top of the list
	if (code == APP_LOCALE_REGION) {
		options.unshift(obj, {
			value: '-',
			text: '–',
			disabled: true
		});
	}

	// Country in current loop
	options.push(obj);
});

// Add empty value
options.unshift({
	value: '', // Default value in db
	text: i18n.t('common.actions.select') + '...'
});

export default options;