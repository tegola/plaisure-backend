export default {
	// Common elements --------------------------------------------------------
	navbar: {
		search: 'Search...',
		dropdown: {
			venues: 'Your venues',
			add: 'Add your venue',
			edit: 'Edit your profile',
			logout: 'Logout'
		}
	},

	footer: {
		venues: {
			title: 'Venues',
			search: 'Search',
			in: 'Venues in {city}',
			promote: 'Promote your venue'
		},
		company: {
			title: 'Company',
			about: 'About',
			contact: 'Contact'
		},
		gaming: {
			title: 'Responsible playing',
			responsibly: 'Play responsibly',
			rules: 'The rules',
			myths: 'Myths',
			help: 'Where to ask for help'
		},
		info: 'Informati sulle probabilità di vincita e sul regolamento di gioco sul sito {0}.', // FIXME
		rating: 'Il gioco è vietato{break}ai minori di {age} anni', // FIXME
		copyright: 'Copyright {year} {company}',
		vat: 'VAT {number}',
	},

	// Login page -------------------------------------------------------------
	login: {
		title: 'Sign in',
		intro: 'Type your e-mail and password to sign in to {name}',
		email: 'E-mail address',
		email_error: 'Type your e-email address.',
		password: 'Password',
		password_error: 'Type your password.',
		submit: 'Sign in',
		forgot: 'Forgot password?',
		register1: 'Not registered yet? {link}',
		register2: 'Register now!'
	},

	// Register page ----------------------------------------------------------
	register: {
		title: 'Register to {name}',
		intro: 'You will be able to claim your venue or publish a new one',
		name: 'Name',
		name_error: 'Type your name.',
		email: 'E-mail address',
		email_error: 'Type your e-email address.',
		password: 'Password',
		password_error: 'Choose a password.',
		agree1: 'Clicking on Register you agree to our {terms_link}. Find out how we use your data by reading our {privacy_link}.',
		agree2: 'Terms of Service',
		agree3: 'Privacy policy',
		submit: 'Register',
		login1: 'Already registered? {link}',
		login2: 'Sign in'
	},

	// About page -------------------------------------------------------------
	about: {
		company: {
			title: 'About {name}',
			paragraph1: "{name} è una start-up che combina le capacità tecnologiche di web e di design da un lato, e l'esperienza nel settore gioco a livello italiano e mondiale dall'altro.",  // FIXME
			paragraph2: 'Nel panorama mondiale del settore gioco mancava un servizio come {name}, dove si fanno incontrare i due attori della filiera — chi cerca e chi offre gioco lecito — garantendo un livello sempre più alto per gli ospiti delle case da gioco, che potranno scegliere, anche attraverso {name}, dove passare il proprio prezioso tempo libero.',  // FIXME
			paragraph3: "L'utente potrà consultare tutte le informazioni come ad esempio il numero di macchine, la tipologia di slot machines e VLT, di giochi live, gli orari di apertura, la ristorazione, le scommesse, ecc., i benefit e gli eventi che la sala da gioco ha da offrire.",  // FIXME
			paragraph4: 'Il gestore, attraverso {name}, potrà comunicare con potenziali clienti con una semplicità senza precedenti nel settore gioco.',  // FIXME
			paragraph5: "{name} è sensibile al gioco responsabile dando una visibilità preferenziale alle case da gioco sicure e con personale qualificato con attestati di frequenza a corsi per contrastare il Gioco d'Azzardo Patologico (GAP)."  // FIXME
		},
		contact: {
			title: 'Contact us',
			intro: "Drop us a line at one of the email addresses below. We'll do our best to reply as soon as possible.",
			info: 'For generic information:',
			venues: 'To add a new venue or claim an existing one:',
			report: 'To report an issue:'
		}
	}
};