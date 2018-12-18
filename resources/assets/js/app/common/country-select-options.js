import i18n from 'prontogioco/app/lang';
import { APP_LOCALE } from 'prontogioco/constants';

const [language, region] = APP_LOCALE.split(/_|-/); // it_IT -> it, IT
const countries = require(`@umpirsky/country-list/data/${language}/country.json`);

const options = [];

Object.keys(countries).forEach(code => {
	const obj = {
		value: code,
		text: countries[code]
	};

	// Add current country + separator to top of the list
	if (code == region) {
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