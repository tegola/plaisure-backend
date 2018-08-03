export default {
	// Common elements --------------------------------------------------------
	navbar: {
		search: 'Cerca vicino a...',
		dropdown: {
			venues: 'Le tue attività',
			add: 'Aggiungi la tua attività',
			edit: 'Modifica i tuoi dati',
			logout: 'Esci'
		}
	},

	footer: {
		venues: {
			title: 'Sale ed esercizi',
			search: 'Ricerca',
			in: 'Esercizi a {city}',
			promote: 'Promuovi la tua attività'
		},
		company: {
			title: 'Azienda',
			about: 'Chi siamo',
			contact: 'Contatti'
		},
		gaming: {
			title: 'Gioco responsabile',
			responsibly: 'Gioca senza esagerare',
			rules: 'Le regole',
			myths: 'Miti e credenze',
			help: 'Dove chiedere aiuto'
		},
		info: 'Informati sulle probabilità di vincita e sul regolamento di gioco sul sito {0}.',
		rating: 'Il gioco è vietato{break}ai minori di {age} anni',
		copyright: 'Copyright {year} {company}',
		vat: 'P. IVA {number}',
	},

	// Login page -------------------------------------------------------------
	login: {
		title: 'Accedi',
		intro: 'Inserisci e-mail e password per accedere a {name}',
		email: 'Indirizzo e-mail',
		email_error: 'Inserisci il tuo indirizzo e-mail.',
		password: 'Password',
		password_error: 'Inserisci la password.',
		submit: 'Accedi',
		forgot: 'Password dimenticata?',
		register1: 'Non sei ancora iscritto? {link}',
		register2: 'Fallo subito!'
	},

	// Register page ----------------------------------------------------------
	register: {
		title: 'Iscriviti a {name}',
		intro: 'Potrai così registrare o modificare la tua attività.',
		name: 'Nome',
		name_error: 'Inserisci il tuo nome.',
		email: 'Indirizzo e-mail',
		email_error: 'Inserisci il tuo indirizzo e-mail.',
		password: 'Password',
		password_error: 'Scegli una password.',
		agree1: 'Cliccando su Iscriviti accetti le nostre {terms_link}. Scopri in che modo usiamo i tuoi dati nella nostra {privacy_link}.',
		agree2: 'Condizioni',
		agree3: 'Normativa sui dati',
		submit: 'Iscriviti',
		login1: 'Sei già registrato? {link}',
		login2: 'Accedi'
	},

	// About page -------------------------------------------------------------
	about: {
		company: {
			title: "Che cos'è {name}",
			paragraph1: "{name} è una start-up che combina le capacità tecnologiche di web e di design da un lato, e l'esperienza nel settore gioco a livello italiano e mondiale dall'altro.",
			paragraph2: 'Nel panorama mondiale del settore gioco mancava un servizio come {name}, dove si fanno incontrare i due attori della filiera — chi cerca e chi offre gioco lecito — garantendo un livello sempre più alto per gli ospiti delle case da gioco, che potranno scegliere, anche attraverso {name}, dove passare il proprio prezioso tempo libero.',
			paragraph3: "L'utente potrà consultare tutte le informazioni come ad esempio il numero di macchine, la tipologia di slot machines e VLT, di giochi live, gli orari di apertura, la ristorazione, le scommesse, ecc., i benefit e gli eventi che la sala da gioco ha da offrire.",
			paragraph4: 'Il gestore, attraverso {name}, potrà comunicare con potenziali clienti con una semplicità senza precedenti nel settore gioco.',
			paragraph5: "{name} è sensibile al gioco responsabile dando una visibilità preferenziale alle case da gioco sicure e con personale qualificato con attestati di frequenza a corsi per contrastare il Gioco d'Azzardo Patologico (GAP).",
		},
		contact: {
			title: 'Contattaci',
			intro: 'Scrivici a uno dei seguenti indirizzi. Sarà nostra cura risponderti al più presto.',
			info: 'Per informazioni generiche:',
			venues: "Per aggiungere o rivendicare un'attività:",
			report: 'Per segnalare un errore:'
		}
	}
};