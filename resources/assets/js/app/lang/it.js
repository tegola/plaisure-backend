export default {
	// Common -----------------------------------------------------------------
	common: {
		weekdays: {
			monday: 'Lunedì',
			tuesday: 'Martedì',
			wednesday: 'Mercoledì',
			thursday: 'Giovedì',
			friday: 'Venerdì',
			saturday: 'Sabato',
			sunday: 'Domenica'
		},
		status: {
			offline_warning: 'Attenzione: non sei connesso a internet!',
			error: 'Error',
			loading: 'Caricamento'
		},
		actions: {
			cancel: 'Annulla',
			save: 'Salva',
			delete: 'Elimina',
			remove: 'Rimuovi'
		}
	},

	// Components -------------------------------------------------------------
	components: {
		navbar: {
			search: 'Cerca vicino a...',
			dropdown: {
				venues: 'Le tue attività',
				add: 'Aggiungi la tua attività',
				edit: 'Modifica i tuoi dati',
				logout: 'Esci'
			}
		},

		lightbox: {
			previous: 'Precedente',
			next: 'Seguente',
			close: 'Chiudi',
			counter: '{current} di {total}'
		},

		pane: {
			loading: 'Caricamento'
		},

		modal: {
			cancel: 'Annulla'
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
			vat: 'P. IVA {number}'
		}
	},

	// Pages ------------------------------------------------------------------
	pages: {
		home: {
			title: 'Cerca le sale da gioco più vicine a te, trova i jackpot più alti e&nbsp;vinci!',
			subtitle: 'Più di 5000 sale tra cui&nbsp;scegliere!',
			search: 'Cerca',
			city_placeholder: 'Scrivi la tua città',
			location_placeholder: 'Vicino a te',
			location: 'Usa la tua posizione',
			location_error: 'Non è stato possibile trovare la tua posizione.',
			submit: 'Cerca',
			explore: {
				intro: 'Ti senti fortunato?',
				title: 'Esplora la tua zona'
			},
			promote: {
				intro: 'Sei nel campo?',
				title: 'Promuovi la tua attività'
			},
			play_responsibly: {
				intro: 'Non esagerare',
				title: 'Gioca responsabilmente'
			}
		},

		login: {
			meta_title: 'Accedi',
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

		register: {
			meta_title: 'Iscriviti',
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

		explore: {
			loading: 'Caricamento',
			view: {
				list: 'Mostra lista',
				map: 'Mostra mappa'
			},
			filters: {
				select: 'Scegli',
				all: 'Tutti',
				selected: 'Nessuna selezione | 1 selezionato | {count} selezionati',
				category_label: 'Tipo',
				radius_label: 'Distanza',
				amenity_label: 'Servizi disponibili'
			},
			placeholder: {
				location: 'Vicino a te',
				in_map: "All'interno della mappa"
			},
			location: 'Usa la tua posizione',
			location_error: 'Non è stato possibile trovare la tua posizione.',
			results: 'Nessuna attività trovata | 1 risultato | {count} risultati',
			limited_results: 'Il numero di risultati è stato limitato automaticamente. Cerca una zona specifica per visualizzare più dettagli.',
			search_area: 'Cerca in questa zona',
			no_items: {
				title: 'Nessuna attività trovata',
				subtitle: 'Cerca in un altra zona o modifica i criteri ricerca.'
			}
		},

		venue_detail: {
			subtitle: '{category} a {city}',
			gallery: {
				add: 'Aggiungi foto',
				all: 'Guarda tutte le foto'
			},
			description: 'Descrizione attività',
			details: {
				title: 'Dettagli',
				concessionaire: 'Concessionario',
				surface_size: 'Dimensioni',
				vlt_machine_count: 'Numero di VLT',
				vlt_platforms: 'Piattaforme VLT',
				awp_machine_count: 'Numero di AWP',
				arcade_roulette: 'Roulette arcade',
				online_casino: 'Casinò online',
				sports_betting: 'Scommesse sportive',
				virtual_betting: 'Scommesse virtuali',
				horse_betting: 'Scommesse ippiche',
				parking_capacity: 'Posti auto',
				pay_per_view_platforms: 'Pay per view disponibili',
				seating_capacity: 'Posti a sedere'
			},
			amenities: {
				title: 'Servizi',
				atm: 'Totem Bancomat',
				bar: 'Bar',
				pay_per_view: 'Pay per view',
				pos: 'POS',
				private_parking: 'Parcheggio privato',
				restaurant: 'Ristorante',
				security: 'Servizio di sicurezza',
				smoking_area: 'Area fumatori',
				wifi: 'Wi-Fi'
			},
			card: {
				directions: 'Ottieni indicazioni stradali',
				closed: 'Chiuso',
				open_now: 'Aperto ora',
				closed_now: 'Chiuso ora',
				no_hours: 'Nessun orario',
				no_contact: 'Nessuna informazione di contatto',
				no_urls: 'Nessun sito o pagina social'
			},
			claim: {
				title: 'È la tua attività?',
				intro: 'Se sei proprietaro o gestore di questa attività, puoi rivendicarla gratuitamente e tenerla aggiornata, aggiungere foto, jackpot e tanto altro.',
				more: 'Ulteriori informazioni',
				action: 'Rivendica attività',
				subject: 'Rivendicazione attività: {name} (identificativo: {id})'
			},
			issues: {
				title: 'Hai trovato un errore?',
				intro: "Se l'indirizzo o i dati sono errati, l'attività non esiste più, o se ci sono foto offensive, puoi {report}.",
				report: 'segnalare questa attività',
				subject: 'Segnalazione errore: {name} (identificativo: {id})'
			},
			nearby: 'Attività nei dintorni',
			common: {
				edit: 'modifica',
				unknown: 'sconosciuto',
				yes: 'Sì',
				no: 'No'
			}
		},

		venue_form: {
			title: {
				add: 'Aggiungi attività',
				edit: 'Modifica attività'
			},
			loading: 'Caricamento',
			save: 'Salva',
			general: {
				title: 'Generale',
				name: 'Nome',
				name_placeholder: 'Es.: Casinò Las Vegas',
				name_error: 'Inserisci il nome della tua attività.',
				concessionaire: 'Concessionario',
				concessionaire_none: 'Nessuno',
				description: 'Descrizione',
				surface_size: 'Dimensioni',
				surface_size_unit: 'mq.',
				surface_size_error: 'Inserisci le dimensioni.',
				category: 'Categoria',
				category_error: 'Scegli almeno una categoria.',
				address: 'Indirizzo',
				address_placeholder1: 'Via',
				address_placeholder2: 'Numero civico',
				address_error: "Inserisci tutti i dati dell'indirizzo.",
				city: 'Città',
				zipcode_province: 'CAP e provincia',
				zipcode_placeholder: 'CAP',
				province_placeholder: 'Provincia',
				location: 'Posizione esatta',
				location_searching: 'Cerco',
				location_hint: 'Trascina per riposizionare'
			},
			services: {
				title: 'Servizi',
				invalid_value: 'Valore non valido.',
				sports_betting: 'Scommesse sportive',
				virtual_betting: 'Scommesse virtuali',
				horse_betting: 'Scommesse ippiche',
				arcade_roulette: 'Roulette arcade',
				vlt_machine_count: 'N. macchine VLT',
				awp_machine_count: 'N. macchine AWP',
				seating_capacity: 'Posti a sedere',
				parking_capacity: 'Posti auto',
				vlt_platforms: 'Piattaforme VLT',
				amenities: {
					title: 'Comodità',
					atm: 'Totem Bancomat',
					bar: 'Bar',
					pay_per_view: 'Pay per view',
					pos: 'POS',
					private_parking: 'Parcheggio privato',
					restaurant: 'Ristorante',
					security: 'Security',
					smoking_area: 'Area fumatori',
					wifi: 'Wi-Fi'
				},
				pay_per_view_platforms: 'Piattaforme Pay Per View'
			},
			contacts: {
				title: 'Contatti',
				phone: 'Telefono',
				email: 'E-mail',
				email_placeholder: 'Es.: nome@gmail.com',
				email_error: 'Inserisci un indirizzo e-mail valido.',
				url_placeholder: 'https://',
				url_error: "Inserisci un URL valido, che inizi con 'http://' o 'https://'.",
				site: 'Sito web',
				online_casino: 'Casinò online',
				facebook: 'Pagina Facebook'
			},
			hours: {
				title: 'Orari',
				always: 'Sempre aperto (24h)',
				full: 'Orario continuato',
				split: 'Orario spezzato',
				closed: 'Chiuso',
				from_to: 'Dalle/alle',
				morning: 'Mattina (dalle/alle)',
				afternoon: 'Pomeriggio (dalle/alle)'
			},
			photos: {
				title: 'Foto',
				upload: 'Carica foto',
				remove: {
					title: 'Rimuovi photo',
					intro: "Stai per {action}. Essa verrà effettivamente eliminata dalla galleria una volta salvati i dati dell'attività.",
					intro_action: 'rimuovere questa foto'
				}
			},
			jackpots: {
				title: 'Jackpot',
				name: 'Jackpot {number}',
				name_placeholder: 'Nome',
				amount_placeholder: 'Valore',
				amount_error: 'Inserisci un numero valido.'
			}
		},

		about: {
			meta_title: 'Informazioni e contatti',
			company: {
				title: "Che cos'è {name}",
				paragraph1: "{name} è una start-up che combina le capacità tecnologiche di web e di design da un lato, e l'esperienza nel settore gioco a livello italiano e mondiale dall'altro.",
				paragraph2: 'Nel panorama mondiale del settore gioco mancava un servizio come {name}, dove si fanno incontrare i due attori della filiera — chi cerca e chi offre gioco lecito — garantendo un livello sempre più alto per gli ospiti delle case da gioco, che potranno scegliere, anche attraverso {name}, dove passare il proprio prezioso tempo libero.',
				paragraph3: "L'utente potrà consultare tutte le informazioni come ad esempio il numero di macchine, la tipologia di slot machines e VLT, di giochi live, gli orari di apertura, la ristorazione, le scommesse, ecc., i benefit e gli eventi che la sala da gioco ha da offrire.",
				paragraph4: 'Il gestore, attraverso {name}, potrà comunicare con potenziali clienti con una semplicità senza precedenti nel settore gioco.',
				paragraph5: "{name} è sensibile al gioco responsabile dando una visibilità preferenziale alle case da gioco sicure e con personale qualificato con attestati di frequenza a corsi per contrastare il Gioco d'Azzardo Patologico (GAP)."
			},
			contact: {
				title: 'Contattaci',
				intro: 'Scrivici a uno dei seguenti indirizzi. Sarà nostra cura risponderti al più presto.',
				info: 'Per informazioni generiche:',
				venues: "Per aggiungere o rivendicare un'attività:",
				report: 'Per segnalare un errore:'
			}
		},

		play_responsibly: {
			meta_title: 'Gioca senza esagerare'
		},

		error: {
			not_found: 'Pagina non trovata',
			server_error: 'Errore del server'
		}
	},

	// Database ---------------------------------------------------------------
	db: {
		categories: {
			betting_agency: 'Agenzia scommesse',
			bingo: 'Bingo',
			vlt: 'Sala slot VLT'
		}
	}
};