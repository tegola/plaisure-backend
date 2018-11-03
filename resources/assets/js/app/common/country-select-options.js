import i18n from 'prontogioco/app/lang';

const locale = i18n.locale;
const countries = require(`@umpirsky/country-list/data/${locale}/country.json`);

const options = [{
	value: null,
	text: i18n.t('common.actions.select') + '...'
}];

Object.keys(countries).forEach(code => {
	options.push({
		value: code,
		text: countries[code]
	});
});

export default options;