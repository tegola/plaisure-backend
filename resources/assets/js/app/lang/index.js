import Vue from 'vue';
import VueI18n from 'vue-i18n';
import axios from 'prontogioco/app/plugins/axios';

const loadedLanguages = [];

// Init plugin
Vue.use(VueI18n);

const i18n = new VueI18n();

export default i18n;

// Help functions
function setLanguage(lang) {
	i18n.locale = lang;
	axios.defaults.headers.common['Accept-Language'] = lang;

	return lang;
}

export function loadLanguage(lang) {
	if (i18n.locale !== lang) {
		if (!loadedLanguages.includes(lang)) {
			return import(`./${lang}`).then(msgs => {
				i18n.setLocaleMessage(lang, msgs.default);
				loadedLanguages.push(lang);

				return setLanguage(lang);
			});
		}
		return Promise.resolve(setLanguage(lang));
	}
	return Promise.resolve(lang);
}

// Load env locale
loadLanguage(process.env.MIX_LOCALE);