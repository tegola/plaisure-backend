import i18n from '@/lang';
import { APP_LOCALE_LANGUAGE, APP_LOCALE_REGION } from '@/constants';
const countries = require(`@umpirsky/country-list/data/${APP_LOCALE_LANGUAGE}/country.json`);

const options = [];

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