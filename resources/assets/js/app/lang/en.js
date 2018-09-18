export default {
	// Common -----------------------------------------------------------------
	common: {
		weekdays: {
			monday: 'Monday',
			tuesday: 'Tuesday',
			wednesday: 'Wednesday',
			thursday: 'Thursday',
			friday: 'Friday',
			saturday: 'Saturday',
			sunday: 'Sunday'
		},
		status: {
			offline_warning: "Attention: you're not connected to the internet!",
			error: 'Error',
			loading: 'Loading',
			invalid_form: 'There are errors in the form. Please review your entry.',
			save_error: 'There was an error while trying saving data. Please try again.'
		},
		actions: {
			add: 'Add',
			edit: 'Edit',
			cancel: 'Cancel',
			save: 'Save',
			delete: 'Delete',
			remove: 'Remove',
			select: 'Select'
		}
	},

	// Components -------------------------------------------------------------
	components: {
		navbar: {
			search: 'Search...'
		},

		lightbox: {
			previous: 'Previous',
			next: 'Next',
			close: 'Close',
			counter: '{current} of {total}'
		},

		no_items: {
			title: 'No items found'
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
			vat: 'VAT {number}'
		}
	},

	// Pages ------------------------------------------------------------------
	pages: {
		home: {
			title: 'Search game rooms nearby, find the highest jackpots and&nbsp;win!',
			subtitle: 'Over 5000 venues available!',
			search: 'Search',
			city_placeholder: 'Type your city name',
			location_placeholder: 'Near you',
			location: 'Use your current location',
			location_error: "We couldn't find your location.",
			submit: 'Search',
			explore: {
				intro: 'Feeling lucky?',
				title: 'Explore venues nearby'
			},
			promote: {
				intro: "It's your daily job?",
				title: 'Promote your venue'
			},
			play_responsibly: {
				intro: 'Be careful',
				title: 'Play responsibly'
			}
		},

		login: {
			meta_title: 'Sign in',
			title: 'Sign in',
			intro: 'Type your e-mail and password to sign in to {name}',
			email: 'E-mail address',
			email_error: 'Type your e-email address.',
			password: 'Password',
			password_error: 'Type your password.',
			submit: 'Sign in',
			submit_error: 'Incorrect e-mail or password.',
			forgot: 'Forgot password?',
			register1: 'Not registered yet? {link}',
			register2: 'Register now!'
		},

		register: {
			meta_title: 'Register',
			title: 'Register to {name}',
			intro: 'You will be able to claim your venue or publish a new one',
			name: 'Name',
			name_error: 'Type your name.',
			email: 'E-mail address',
			email_error: 'Type your e-email address.',
			password: 'Password',
			password_error: 'Choose a password (8 characters minimum).',
			agree1: 'Clicking on Register you agree to our {terms_link}. Find out how we use your data by reading our {privacy_link}.',
			agree2: 'Terms of Service',
			agree3: 'Privacy policy',
			submit: 'Register',
			login1: 'Already registered? {link}',
			login2: 'Sign in'
		},

		forgot_password: {
			title: 'Reset password',
			intro: 'Insert your e-mail address to receive a link to reset your password.',
			email: 'E-mail address',
			email_error: 'Type your e-email address.',
			submit: 'Send link',
			submit_success: 'Done! If this e-mail address is a valid account you will get a message with a link to reset your password.'
		},

		reset_password: {
			title: 'Reset password',
			intro: 'Set a new password for your account.',
			email: 'E-mail address',
			email_error: 'Type your e-email address.',
			password: 'Password',
			password_error: 'Choose a password (8 characters minimum).',
			password_confirmation: 'Repeat password',
			password_confirmation_error: "Passwords don't match.",
			submit: 'Reset password',
			submit_error: "Your password couldn't be reset. Please try again."
		},

		explore: {
			meta_title: 'Explore',
			view: {
				list: 'Show list',
				map: 'Show map'
			},
			filters: {
				all: 'All',
				selected: 'No selection | 1 selected | {count} selected',
				category_label: 'Type',
				radius_label: 'Distance',
				amenity_label: 'Amenities'
			},
			placeholder: {
				location: 'Near you',
				in_map: 'Inside map bounds'
			},
			location: 'Use your current location',
			location_error: "We couldn't find your location.",
			results: 'No venues found | 1 result | {count} results',
			limited_results: 'Results have been limited. Search a specific area to get more details.',
			search_area: 'Search this area',
			no_items: {
				title: 'No venues found',
				subtitle: 'Move to a different area or change search filters.'
			}
		},

		venue_detail: {
			subtitle: '{category} in {city}',
			gallery: {
				add: 'Add photo',
				all: 'See all'
			},
			description: 'Description',
			details: {
				title: 'Details',
				concessionaire: 'Concessionaire',
				surface_size: 'Surface size',
				vlt_machine_count: 'VLC machines',
				vlt_platforms: 'VLT platforms',
				awp_machine_count: 'AWP machines',
				arcade_roulette: 'Arcade roulette',
				online_casino: 'Online casinò',
				sports_betting: 'Sports betting',
				virtual_betting: 'Virtual betting',
				horse_betting: 'Horse betting',
				parking_capacity: 'Parking spots',
				pay_per_view_platforms: 'Pay Per View',
				seating_capacity: 'Seatings'
			},
			amenities: {
				title: 'Amenities',
				atm: 'ATM',
				bar: 'Bar',
				pay_per_view: 'Pay Per View',
				pos: 'POS',
				private_parking: 'Private parking spots',
				restaurant: 'Restaurant',
				security: 'Security',
				smoking_area: 'Smoking area',
				wifi: 'Wi-Fi'
			},
			card: {
				directions: 'Get directions',
				closed: 'Closed',
				open_now: 'Open now',
				closed_now: 'Closed',
				no_hours: 'No business hours info',
				no_contact: 'No contact info',
				no_urls: 'No site or social network profile'
			},
			claim: {
				title: 'Is this your venue?',
				intro: "If you're the owner or the manager of this venue, you can claim it for free and keep it updated, add photos, jackpots, and more.",
				more: 'More info',
				action: 'Claim this venue',
				subject: 'Claim venue: {name} (id: {id})'
			},
			issues: {
				title: 'Found an error?',
				intro: 'If the address is incorrect, the venue has shut down, or if there are offensive photos, you can {report}.',
				report: 'report this venue',
				subject: 'Report: {name} (id: {id})'
			},
			nearby: 'Venues nearby',
			common: {
				edit: 'edit',
				unknown: 'unknown',
				yes: 'Yes',
				no: 'No'
			}
		},

		venue_form: {
			title: {
				add: 'Add venue',
				edit: 'Edit venue'
			},
			general: {
				title: 'General info',
				name: 'Name',
				name_placeholder: 'Ex.: Las Vegas Casinò',
				name_error: 'Type the venue name.',
				concessionaire: 'Concessionaire',
				concessionaire_none: 'None',
				description: 'Description',
				surface_size: 'Surface size',
				surface_size_unit: 'm²',
				surface_size_error: "Insert the venue's surface size",
				category: 'Category',
				category_error: 'Pick at least a category.',
				address: 'Address',
				address_placeholder1: 'Street',
				address_placeholder2: 'Street number',
				address_error: 'Fill in all address data',
				city: 'City',
				zipcode_province: 'Zip code & province',
				zipcode_placeholder: 'Zip code',
				province_placeholder: 'Province',
				location: 'Location',
				location_searching: 'Searching',
				location_hint: 'Drag to reposition'
			},
			services: {
				title: 'Services',
				invalid_value: 'Invalid value.',
				sports_betting: 'Sports betting',
				virtual_betting: 'Virtual betting',
				horse_betting: 'Horse betting',
				arcade_roulette: 'Arcade roulette',
				vlt_machine_count: 'VLC machines',
				awp_machine_count: 'AWP machines',
				seating_capacity: 'Seatings',
				parking_capacity: 'Parking spots',
				vlt_platforms: 'VLT platforms',
				amenities: {
					title: 'Amenities',
					atm: 'ATM',
					bar: 'Bar',
					pay_per_view: 'Pay Per View',
					pos: 'POS',
					private_parking: 'Private parking spots',
					restaurant: 'Restaurant',
					security: 'Security',
					smoking_area: 'Smoking area',
					wifi: 'Wi-Fi'
				},
				pay_per_view_platforms: 'Pay Per View'
			},
			contacts: {
				title: 'Contacts',
				phone: 'Phone number',
				email: 'E-mail',
				email_placeholder: 'Ex.: name@gmail.com',
				email_error: 'Type a valid e-mail address..',
				url_placeholder: 'https://',
				url_error: "Type a valid URL, starting with 'http://' or 'https://'.",
				site: 'Website URL',
				online_casino: 'Online casinò',
				facebook: 'Facebook page'
			},
			hours: {
				title: 'Business hours',
				always: 'Always open (24h)',
				full: 'All day',
				split: 'Split hours',
				closed: 'Closed',
				from_to: 'From/to',
				morning: 'Morning (from/to)',
				afternoon: 'Afternoon (from/to)'
			},
			photos: {
				title: 'Photos',
				upload: 'Upload photo',
				remove: {
					title: 'Remove photo',
					intro: "You're about to {action}. It will be actually removed from the gallery once you save this venue data.",
					intro_action: 'remove this photo'
				}
			},
			jackpots: {
				title: 'Jackpots',
				name: 'Jackpot {number}',
				name_placeholder: 'Name',
				amount_placeholder: 'Amount',
				amount_error: 'Type a valid amount.'
			}
		},

		user: {
			meta_title: 'User profile',
			title: 'Hello {name}!',
			actions: {
				add_venue: 'Add a venue',
				edit_profile: 'Edit your profile',
				logout: 'Logout'
			},
			no_items: {
				title: 'No venues found',
				message: "You haven't added your venue(s) yet. {action} to start now!",
				message_action: 'Click here'
			}
		},

		user_form: {
			meta_title: 'Edit your profile',
			title: 'Edit your profile',
			general: {
				name: 'First and last name',
				name_error: 'Please type your first and last name.',
				email: 'E-mail address',
				newsletter: 'Keep me informed with new features and deals'
			},
			billing: {
				title: 'Billing',
				legal_name: 'Legal name',
				legal_name_error: 'Please type your company legal name.',
				address: 'Address',
				address_error: 'Please type the address.',
				postcode: 'Postal code',
				postcode_error: 'Please type the postal code.',
				city: 'City',
				city_error: 'Please type the city name.',
				region: 'Region',
				region_error: 'Please type the region.',
				country: 'Country',
				country_error: 'Please type the country name.',
				vat_number: 'VAT number',
				vat_number_error: 'Please type the VAT number.'
			},
			password: {
				title: 'Pick new password',
				intro: 'Type a new password below to change it, leave the two fields empty to keep it unchanged.',
				password: 'New password',
				password_hint: 'At least 8 chars, letters and numbers',
				password_error: 'Please type at least 8 chars, letters and numbers.',
				password_confirmation: 'Repeat password',
				password_confirmation_error: "Passwords don't match."
			}
		},

		promote: {
			meta_title: 'Promote your venue',
			hero: {
				title: "Promote your venue, it's free!",
				paragraph: "You're the owner of a game-related business looking for new ways to promote it? Your venue is already listed on {name} and you would like to show more information to visitors? In both cases, {strong}",
				paragraph_strong: "you're in the right place!"
			},
			example: {
				paragraph1: 'Adding or claiming an existing venue will allow you to reach to future clients by just adding basic information like business hours, photos, machine count and machine types, benefits, venue description and more.',
				paragraph2: 'Here you an example venue with additional information, like photos {1}, custom jackpots {2}, details and services {3}, and contact information {4}.',
				paragraph3: "The more the information, the more you'll gather visitors. For example, displaying the business hours allows night owls to know if your venue is open in less canon hours.",
				paragraph4: '{name} is often consulted by players that want to spend their free time, and they could be choosing you!'
			},
			cost: {
				title: 'How much will it cost?',
				paragraph1: "Absolutely nothing! {name} provides different plans: {strong}; you won't spend anything except a little time.",
				paragraph1_strong: 'adding a venue or claiming it to add basic information is completely free',
				paragraph2: 'Instead, if you wish to highlight and make your business easier to find, {contact} to try one of our premium plans at special launch prices.',
				paragraph2_contact: 'contact us'
			},
			start: {
				title: 'What are you waiting? Start now!',
				paragraph1: "If you're still doubtful, you really shouldn't. Adding or claiming your venue will only bring benfits. Our team will deal with the necessary work to update your business information, and soon you will be able to make changes by yourself!",
				paragraph2: 'If you already found your business on {name}, click on “{strong1}” in the business detail page to contact us. Otherwise just drop us a line at {email} specifying the venue name and the physical addreess. {strong2}',
				paragraph2_strong1: 'Claim this venue',
				paragraph2_strong2: 'In both cases, our team will get in touch as soon as possible.'
			},
			contact: 'Contact us'
		},

		about: {
			meta_title: 'About & Contact',
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
		},

		play_responsibly: {
			meta_title: 'Play responsibly'
		},

		error: {
			not_found: 'Page not found',
			server_error: 'Server error'
		}
	},

	// Database ---------------------------------------------------------------
	db: {
		categories: {
			betting_agency: 'Betting Agency',
			bingo: 'Bingo',
			vlt: 'VLT Games Room'
		},
		amenities: {
			atm: 'ATM',
			bar: 'Bar',
			pay_per_view: 'Pay Per View',
			pos: 'POS',
			private_parking: 'Private parking spots',
			restaurant: 'Restaurant',
			security: 'Security',
			smoking_area: 'Smoking area',
			wifi: 'Wi-Fi'
		}
	}
};