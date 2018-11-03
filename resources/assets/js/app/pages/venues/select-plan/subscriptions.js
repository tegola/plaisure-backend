import i18n from 'prontogioco/app/lang';

export default [
	{
		name: i18n.t('db.subscriptions.default'),
		machine_name: 'default',
		price: 0,
		lines: [
			'Indirizzo e posizione geografica',
			'Contatti e orari di apertura',
			'Servizi disponibili',
			'Normale posizionamento nei risultati di ricerca',
			'Carica fino a 5 foto'
		]
	},
	{
		name: i18n.t('db.subscriptions.premium_1'),
		machine_name: 'premium_1',
		price: 39,
		lines: [
			'Le funzionalità del piano gratuito',
			'Bonus di 5 km nei risultati di ricerca',
			'Visibilità ricorrente in home page',
			'Carica fino a 20 foto'
		],
		highlight: true
	},
	{
		name: i18n.t('db.subscriptions.premium_2'),
		machine_name: 'premium_2',
		price: 79,
		lines: [
			'Le funzionalità del piano gratuito',
			'Bonus di 10 km nei risultati di ricerca',
			'Visibilità ricorrente in home page',
			'Carica fino a 50 foto',
			'Nasconde le attività vicine nella pagina di dettaglio'
		]
	}
];