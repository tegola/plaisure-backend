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
			error: 'Error',
			loading: 'Caricamento',
			invalid_form: 'Ci sono degli errori nel modulo. Controlla i dati inseriti.',
			save_error: 'È avvenuto un errore nel tentativo di salvare i dati. Prova di nuovo.'
		},
		actions: {
			add: 'Aggiungi',
			edit: 'Modifica',
			cancel: 'Annulla',
			save: 'Salva',
			delete: 'Elimina',
			remove: 'Rimuovi',
			select: 'Scegli'
		}
	},

	// Components -------------------------------------------------------------
	components: {
		offline_alert: {
			offline: 'Attenzione: non sei connesso a internet!'
		},

		navbar: {
			search: 'Cerca vicino a...',
			home: 'Home',
			login: 'Accedi',
			register: 'Iscriviti',
			promote: 'Promuovi la tua attività',
			company: 'Azienda e contatti',
			responsible: 'Gioco responsabile'
		},

		lightbox: {
			previous: 'Precedente',
			next: 'Seguente',
			close: 'Chiudi',
			counter: '{current} di {total}'
		},

		no_items: {
			title: 'Nessun elemento trovato'
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
		},

		venue_item: {
			highlight: {
				new: 'Nuovo!',
				open: 'Aperto ora!'
			}
		}
	},

	// Pages ------------------------------------------------------------------
	pages: {
		home: {
			search: {
				title: 'Trova le sale da gioco più vicine a te.',
				subtitle: "Su {name} è veloce, e con più di {count} sale c'è solo l'imbarazzo della scelta!",
				label: 'Cerca',
				city_placeholder: 'Scrivi la tua città',
				location_placeholder: 'Vicino a te',
				location: 'Usa la tua posizione',
				location_error: 'Non è stato possibile trovare la tua posizione.',
				submit: 'Cerca'
			},
			venue: {
				hint: 'Mostra qui la tua attività'
			},
			explore: {
				title: 'Esplora'
			},
			highlights: {
				title: 'In rilievo'
			},
			new: {
				title: 'Novità'
			},
			promote: {
				intro: 'Scusa il gioco di parole',
				title: 'Mettiti in gioco',
				paragraph: 'Registra la tua attività o rivendica un’attività già presente. È veloce, e soprattutto è gratis!',
				register: 'Registrati come gestore',
				add: 'Aggiungi la tua attività',
				manage: 'Vai alla gestione attività',
				more: 'Maggiori informazioni'
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
			submit_error: 'E-mail e password errate.',
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
			password_error: 'Scegli una password (minimo 8 caratteri).',
			agree1: 'Cliccando su Iscriviti accetti le nostre {terms_link}. Scopri in che modo usiamo i tuoi dati nella nostra {privacy_link}.',
			agree2: 'Condizioni',
			agree3: 'Normativa sui dati',
			submit: 'Iscriviti',
			submit_error: "Questo'indirizzo e-mail è già stato usato.",
			login1: 'Sei già registrato? {link}',
			login2: 'Accedi'
		},

		forgot_password: {
			title: 'Reimposta password',
			intro: 'Inserisci il tuo indirizzo e-mail per ricevere un link per reimpostare la tua password.',
			email: 'Indirizzo e-mail',
			email_error: 'Inserisci il tuo indirizzo e-mail.',
			submit: 'Invia link',
			submit_success: 'Fatto! Se questo indirizzo e-mail esiste nei nostri archivi riceverai un messaggio contenente un link per reimpostare la tua password.'
		},

		reset_password: {
			title: 'Reimposta password',
			intro: 'Scegli la tua nuova password.',
			email: 'Indirizzo e-mail',
			email_error: 'Inserisci il tuo indirizzo e-mail.',
			password: 'Password',
			password_error: 'Scegli una password (minimo 8 caratteri).',
			password_confirmation: 'Ripeti password',
			password_confirmation_error: 'Le password non coincidono.',
			submit: 'Reimposta password',
			submit_error: 'Non è stato possibile reimpostare la tua password. Prova di nuovo.'
		},

		explore: {
			meta_title: 'Esplora',
			view: {
				list: 'Mostra lista',
				map: 'Mostra mappa'
			},
			filters: {
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
				// subject: 'Rivendicazione attività: {name} (identificativo: {id})'
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

		venue_claim: {
			meta_title: 'Rivendica attività: {name}',
			title: 'Rivendica attività',
			intro: 'Stai per rivendicare la seguente attività, che non ha un proprietario o gestore. Così facendo essa sarà assegnata a te e potrai gestirne i dati.',
			continue_code: "Per continuare, inserisci il codice di censimento dell'attività come registrata con l'AAMS:",
			continue_nocode: 'Per proseguire, fai click su “Continua”.',
			code: 'Codice AAMS',
			code_placeholder: 'Codice censimento AAMS',
			code_error: 'Inserisci il codice di censimento AAMS',
			submit: 'Continua',
			submit_error: 'Il codice inserito non è corretto.',
			back: "Torna all'attività",
			infobox: {
				title: 'È gratis!',
				body: "Gestire un'attività è completamente gratuito. Se lo vorrai, potrai sottoscrivere un'abbonamento mensile per promuoverla.",
				action: 'Maggiori informazioni'
			}
		},

		venue_form: {
			title: {
				add: 'Aggiungi attività',
				edit: 'Modifica attività'
			},
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
				address_line1_placeholder: 'Riga 1',
				address_line2_placeholder: 'Riga 2',
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
				url_placeholder: 'http://',
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
				intro: 'Per inserire fino a {count} foto. Per inserirne altre, {action}.',
				intro_action: 'scegli un piano a pagamento',
				upload: 'Carica foto',
				remove: {
					title: 'Rimuovi foto',
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

		user: {
			meta_title: 'Gestione utente',
			title: 'Ciao {name}!',
			actions: {
				add_venue: 'Aggiungi attività',
				edit_profile: 'Modifica i tuoi dati',
				logout: 'Esci'
			},
			no_items: {
				title: 'Nessuna attività',
				message: '{action} per aggiungere la tua prima attività!',
				message_action: 'Clicca qui'
			}
		},

		user_form: {
			meta_title: 'Modifica i tuoi dati',
			title: 'Modifica i tuoi dati',
			general: {
				name: 'Nome e cognome',
				name_error: 'Inserisci nome e cognome.',
				email: 'Indirizzo e-mail',
				newsletter: 'Voglio essere informato sulle nuove offerte e funzionalità'
			},
			billing: {
				title: 'Fatturazione',
				legal_name: "Denominazione legale dell'azienda",
				legal_name_error: "Inserisci la denominazione legale dell'azienda.",
				address: 'Indirizzo',
				address_error: "Inserisci l'indirizzo.",
				postcode: 'CAP',
				postcode_error: 'Inserisci il CAP.',
				city: 'Città',
				city_error: 'Inserisci la città.',
				region: 'Provincia',
				region_error: 'Inserisci la provincia.',
				country: 'Paese',
				country_error: 'Scegli il paese.',
				vat_number: 'Partita IVA',
				vat_number_error: 'Inserisci la partita IVA.'
			},
			password: {
				title: 'Nuova password',
				intro: 'Scrivi una nuova password e ripetila per cambiarla, lascia i due campi vuoti per tenere la password corrente.',
				password: 'Nuova password',
				password_hint: 'Almeno 8 caratteri tra lettere e numeri',
				password_error: 'Inserisci almeno 8 caratteri tra lettere e numeri.',
				password_confirmation: 'Ripeti password',
				password_confirmation_error: 'Le password non coincidono.'
			}
		},

		promote: {
			meta_title: 'Promuovi la tua attività',
			hero: {
				title: 'Promuovi la tua attività, è gratis!',
				paragraph: "Sei il proprietario di un'attività inerente al mondo del gioco e vuoi promuoverla? La tua attività è già presente su {name} e vorresti ampliare le informazioni fornite ai visitatori? In entrambi i casi, {strong}",
				paragraph_strong: 'sei nel posto giusto!'
			},
			example: {
				paragraph1: "Aggiungendo o rivendicando un'attività ti permetterà di arrivare a futuri clienti inserendo le informazioni basilari come gli orari di apertura, foto, numero e tipologia di macchine, benefit, descrizione della tua sala ed altro ancora.",
				paragraph2: "Qui puoi vedere l'aspetto della pagina di una sala d'esempio con informazioni aggiuntive, come foto {1}, jackpot personalizzati {2}, dettagli e servizi {3} e contatti {4}.",
				paragraph3: 'Più informazioni la tua pagina offre, più potenziali clienti potrebbero essere interessati a frequentare la tua sala. Ad esempio, specificare gli orari di apertura permette a chi fa una vita notturna di sapere se la sala è aperta anche in orari meno canonici.',
				paragraph4: '{name} viene consultato dai giocatori che vogliono decidere dove passare il proprio tempo libero, e questi potrebbero scegliere proprio la tua sala!'
			},
			cost: {
				title: 'Quanto costa?',
				paragraph1: 'Assolutamente nulla! {name} offre diversi livelli di personalizzazione: {strong}; non spenderai niente, a parte pochi minuti del tuo tempo.',
				paragraph1_strong: "aggiungere un'attività o rivendicarla per inserire le informazioni essenziali è assolutamente gratuito",
				paragraph2: 'Se invece desideri evidenziare la tua attività e farla trovare più facilmente di quelle dei tuoi concorrenti, {contact} per provare uno dei nostri piani premium a prezzi di lancio molto vantaggiosi.',
				paragraph2_contact: 'scrivici'
			},
			start: {
				title: 'Che aspetti? Inizia subito!',
				paragraph1: 'Se sei ancora indeciso, non dovresti esserlo. Aggiungere o rivendicare la tua attività porterà soltanto benefici. Il nostro staff si occuperà di tutto il necessario per aggiornare i dati, e in futuro potrai anche modificarli in autonomia!',
				paragraph2: "Se hai già trovato la tua attività su {name}, fai click su “{strong1}” nella pagina con i dettagli dell'attività per contattarci. Altrimenti scrivi all'indirizzo {email} ricordandoti di specificare il nome dell'attività e l'indirizzo. {strong2}",
				paragraph2_strong1: 'Rivendica attività',
				paragraph2_strong2: 'In entrambi i casi sarai ricontattato prima possibile dal nostro staff.'
			},
			contact: 'Contattaci'
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

	// Data -------------------------------------------------------------------
	data: {
		subscriptions: {
			default: 'Gratuito',
			premium_1: 'Premium 1',
			premium_2: 'Premium 2'
		},
		amenities: {
			atm: 'Totem Bancomat',
			bar: 'Bar',
			pay_per_view: 'Pay per view',
			pos: 'POS',
			private_parking: 'Parcheggio privato',
			restaurant: 'Ristorante',
			security: 'Servizio di sicurezza',
			smoking_area: 'Area fumatori',
			wifi: 'Wi-Fi'
		}
	}
};