<script>
import { mapState, mapGetters } from 'vuex';
import _extend from 'lodash/extend';
import constants from 'prontogioco/constants';

import PgLightbox from 'prontogioco/app/components/lightbox';
import PgVenueDetailPageContactCard from './contact-card'

import setTitle from 'prontogioco/utilities/set-title';

export default {
	name: 'PgVenueDetailPage',

	components: {
		PgLightbox,
		PgVenueDetailPageContactCard
	},

	props: {
		venueId: {
			type: [String, Number],
			required: true
		}
	},

	filters: {
		formatCurrency(number) {
			return number.toLocaleString(undefined, {
				style: 'currency',
				currency: 'EUR',
				minimumFractionDigits: 2
			})
		}
	},

	data() {
		return {
			loading: false,
			lightboxIndex: 0,
			lightboxOpen: false,
			hoursExpanded: false
		}
	},

	computed: {
		...mapState('venueDetail', [
			'venue',
			'nearbyVenues'
		]),

		...mapGetters('venueDetail', [
			'isOpen',
			'vltPlatformNames',
			'payPerViewPlatformNames',
			'hasJackpots'
		]),

		subtitle() {
			const c = this.venue.categories;

			return [
				c.length ? c[0].name : null,
				c.length ? 'a' : null,
				this.venue.address.city
			].join(' ');
		},

		lightboxImages() {
			const photos = this.venue.photos;

			if (!photos || !photos.length) return null;

			return photos.map(file => ({
				caption: file.caption,
				url: file.resized_url,
				thumbnail_url: file.thumbnail_url
			}));
		}
	},

	watch: {
		venue() {
			setTitle(this.venue ? [this.venue.name, '–', this.subtitle].join(' ') : '');
		}
	},

	methods: {
		loadData() {
			// FIXME: Manca il loader
			// this.loading = true;
			this.$store.dispatch('venueDetail/load', this.venueId);
		},

		isInCategory(categoryMachineName) {
			return this.venue.categories.find(category => category.machine_name == categoryMachineName) ? true : false;
		},

		showLightbox(index) {
			this.lightboxIndex = index;
			this.lightboxOpen = true;
		},

		closeLightbox() {
			this.lightboxOpen = false;
		},

		prepareEmailLink(address, subject) {
			return [
				'mailto:',
				address,
				'?subject=',
				encodeURIComponent(subject)
			].join('')
		}
	},

	beforeCreate() {
		_extend(this, constants);
	},

	mounted() {
		this.loadData()
	}
}
</script>

<template>
	<div class="pg-venue-detail-page">
		<pg-navbar variant="dark" />

		<!-- FIXME: Mostrare un loader mentre si caricano i dati -->
		<template v-if="!loading && venue">
			<!-- Header -->
			<div class="header">
				<div class="container">
					<!-- Gallery -->
					<div class="header-gallery" ref="gallery">
						<div class="header-gallery-bg">
							<div class="header-photo header-photo-placeholder" v-for="i in 6"></div>
						</div>
						<router-link to="/promote" class="header-photo header-photo-add" v-if="!venue.has_owner">
							<pg-icon icon="plus"></pg-icon>
							Aggiungi foto
						</router-link>
						<template v-for="(file, index) in venue.photos">
							<a v-if="index < 10" :href="file.resized_url" class="header-photo" @click.prevent="showLightbox(index)">
								<div class="embed-responsive embed-responsive-1by1 header-photo-img" :style="'background-image: url(' + file.thumbnail_url + ')'">
								</div>
							</a>
							<a v-if="index == 10" :href="file.resized_url" class="header-photo" @click.prevent="showLightbox(index)">
								<div class="embed-responsive embed-responsive-1by1 header-photo-img" :style="'background-image: url(' + file.thumbnail_url + ')'">
									<div class="header-photo-zoom">
										<pg-icon icon="search" class="mb-1"></pg-icon>
										Guarda tutte le foto
									</div>
								</div>
							</a>
						</template>
					</div>

					<!-- Title -->
					<h2 class="header-title">{{ venue.name }}</h2>
					<ul class="list-inline header-subtitle">
						<li class="list-inline-item">{{ subtitle }}</li>
						<li class="list-inline-item" v-if="venue.business_hours.length">
							<span class="text-success" v-if="isOpen">Aperto ora</span>
							<strong class="text-danger" v-else>Chiuso ora</strong>
						</li>
					</ul>
				</div>
			</div>

			<div class="container">

				<div class="row">
					<div class="col-lg-8">
						<!-- Contact card for small screens -->
						<pg-venue-detail-page-contact-card class="d-lg-none" />

						<!-- Jackpots -->
						<template v-if="!venue.has_owner || hasJackpots">
							<div class="row my-5 pt-2">
								<div class="col-md-4" v-for="(jackpot, index) in venue.jackpots">
									<div :class="['jackpot', index < 3 ? 'mb-3 mb-md-0' : null]">
										<img class="jackpot-icon" :src="`/img/detail/jackpot-${index}.svg`">
										<div>
											<div class="jackpot-name">{{ jackpot.label && jackpot.value ? jackpot.label : `Jackpot ${index}` }}</div>
											<div class="jackpot-value">{{ jackpot.value | formatCurrency }}</div>
											<div v-if="!venue.has_owner"><router-link to="/promote">modifica</router-link></div>
										</div>
									</div>
								</div>
							</div>
							<hr>
						</template>

						<!-- Description -->
						<template v-if="venue.description">
							<div class="my-5">
								<h4>Descrizione attività</h4>
								<p>{{ venue.description }}</p>
							</div>
							<hr>
						</template>

						<!-- Details -->
						<div class="my-5">
							<h4>
								Dettagli
								<router-link v-if="!venue.has_owner" to="/promote" class="small ml-2">modifica</router-link>
							</h4>
							<div class="row">
								<div class="col-md">
									<ul class="list-unstyled mb-0 mb-md-3">
										<li class="detail-list-item">
											Concessionario:
											<strong v-if="venue.concessionaire">{{ venue.concessionaire.name }}</strong>
											<span v-else class="text-muted">sconosciuto</span>
										</li>
										<li class="detail-list-item">
											Dimensioni:
											<strong v-if="venue.has_owner">{{ venue.surface_size }} mq.</strong>
											<span v-else class="text-muted">sconosciuto</span>
										</li>
										<li class="detail-list-item">
											Numero di VLT:
											<strong v-if="venue.vlt_machine_count">{{ venue.vlt_machine_count }}</strong>
											<span v-else class="text-muted">sconosciuto</span>
										</li>
										<li class="detail-list-item">
											Piattaforme VLT:
											<strong v-if="venue.vlt_platforms.length">{{ vltPlatformNames }}</strong>
											<span v-else class="text-muted">sconosciuto</span>
										</li>
										<li class="detail-list-item">
											Numero di AWP:
											<strong v-if="venue.awp_machine_count">{{ venue.awp_machine_count }}</strong>
											<span v-else class="text-muted">sconosciuto</span>
										</li>
										<li class="detail-list-item">
											Roulette arcade:
											<strong v-if="venue.arcade_roulette" class="text-success">Sì</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? 'No' : 'sconosciuto' }}</span>
										</li>
										<li class="detail-list-item">
											Casinò online:
											<a v-if="venue.urls.online_casino" :href="venue.urls.online_casino" target="_blank">{{ venue.urls.online_casino }}</a>
											<span v-else class="text-muted">sconosciuto</span>
										</li>
									</ul>
								</div>
								<div class="col-md">
									<ul class="list-unstyled">
										<template v-if="isInCategory('betting_agency')">
											<li class="detail-list-item">
												Scommesse sportive:
												<strong v-if="venue.sports_betting" class="text-success">Sì</strong>
												<span v-else class="text-muted">{{ venue.has_owner ? 'No' : 'sconosciuto' }}</span>
											</li>
											<li class="detail-list-item">
												Scommesse virtuali:
												<strong v-if="venue.virtual_betting" class="text-success">Sì</strong>
												<span v-else class="text-muted">{{ venue.has_owner ? 'No' : 'sconosciuto' }}</span>
											</li>
											<li class="detail-list-item">
												Scommesse ippiche:
												<strong v-if="venue.horse_betting" class="text-success">Sì</strong>
												<span v-else class="text-muted">{{ venue.has_owner ? 'No' : 'sconosciuto' }}</span>
											</li>
										</template>
										<li class="detail-list-item">
											Posti auto:
											<strong v-if="venue.parking_capacity">{{ venue.parking_capacity }}</strong>
											<span v-else class="text-muted">sconosciuto</span>
										</li>
										<li class="detail-list-item">
											Pay per view disponibili:
											<strong v-if="venue.pay_per_view_platforms.length">{{ payPerViewPlatformNames }}</strong>
											<span v-else class="text-muted">sconosciuto</span>
										</li>
										<li class="detail-list-item" v-if="isInCategory('betting_agency')">
											Posti a sedere:
											<strong v-if="venue.seating_capacity">{{ venue.seating_capacity }}</strong>
											<span v-else class="text-muted">sconosciuto</span>
										</li>
									</ul>
								</div>
							</div>
						</div>

						<hr>

						<!-- Amenities -->
						<div class="my-5">
							<h4>
								Servizi
								<router-link v-if="!venue.has_owner" to="/promote" class="small ml-2">modifica</router-link>
							</h4>
							<div class="row">
								<div class="col-md">
									<ul class="list-unstyled mb-0 mb-md-3">
										<li class="detail-list-item">
											Totem Bancomat:
											<strong v-if="venue.amenities.atm" class="text-success">Sì</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? 'No' : 'sconosciuto' }}</span>
										</li>
										<li class="detail-list-item">
											Bar:
											<strong v-if="venue.amenities.bar" class="text-success">Sì</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? 'No' : 'sconosciuto' }}</span>
										</li>
										<li class="detail-list-item">
											Pay per view:
											<strong v-if="venue.amenities.pay_per_view" class="text-success">Sì</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? 'No' : 'sconosciuto' }}</span>
										</li>
										<li class="detail-list-item">
											POS:
											<strong v-if="venue.amenities.pos" class="text-success">Sì</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? 'No' : 'sconosciuto' }}</span>
										</li>
										<li class="detail-list-item">
											Parcheggio privato:
											<strong v-if="venue.amenities.private_parking" class="text-success">Sì</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? 'No' : 'sconosciuto' }}</span>
										</li>
									</ul>
								</div>
								<div class="col-md">
									<ul class="list-unstyled">
										<li class="detail-list-item">
											Ristorante:
											<strong v-if="venue.amenities.restaurant" class="text-success">Sì</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? 'No' : 'sconosciuto' }}</span>
										</li>
										<li class="detail-list-item">
											Servizio di sicurezza:
											<strong v-if="venue.amenities.security" class="text-success">Sì</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? 'No' : 'sconosciuto' }}</span>
										</li>
										<li class="detail-list-item">
											Area fumatori:
											<strong v-if="venue.amenities.smoking_area" class="text-success">Sì</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? 'No' : 'sconosciuto' }}</span>
										</li>
										<li class="detail-list-item">
											Wi-Fi:
											<strong v-if="venue.amenities.wifi" class="text-success">Sì</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? 'No' : 'sconosciuto' }}</span>
										</li>
									</ul>
								</div>
							</div>
						</div>

						<!-- Promote -->
						<div class="card bg-light my-4 text-center">
							<div class="card-body">
								<h4 class="card-title">È la tua attività?</h4>
								<p class="card-text">Se sei proprietaro o gestore di questa attività, puoi rivendicarla gratuitamente e tenerla aggiornata, aggiungere foto, jackpot e tanto altro. <router-link to="/promote">Ulteriori informazioni&hellip;</router-link></p>
								<p class="card-text"><a class="btn btn-primary" :href="prepareEmailLink(EMAIL_VENUES, `Rivendicazione attività: ${venue.name} (identificativo: ${venue.id})`)">Rivendica attività</a></p>
							</div>
						</div>
					</div>
					<div class="col-lg-4">

						<!-- Contact card for big screens -->
						<pg-venue-detail-page-contact-card class="d-none d-lg-block" />

						<!-- Nearby venues -->
						<div class="my-5" v-if="nearbyVenues.length">
							<h5 class="mb-3">Attività nei dintorni</h5>
							<ul class="list-unstyled">
								<li class="d-flex align-items-start" v-for="nearbyVenue in nearbyVenues">
									<img class="mr-3" :src="`/img/map/pin-normal-${nearbyVenue.categories[0].machine_name || 'collapsed'}.svg`">
									<p>
										<strong><router-link :to="`/venues/${nearbyVenue.id}`">{{ nearbyVenue.name }}</router-link></strong><br>
										<span class="initialism text-muted">{{ nearbyVenue.categories[0].name }}</span><br>
										{{ nearbyVenue.address.short }}
									</p>
								</li>
							</ul>
						</div>

						<!-- Report -->
						<div class="my-4">
							<h5>Hai trovato un errore?</h5>
							<p>Se l'indirizzo o i dati sono errati, l'attività non esiste più, o se ci sono foto offensive, puoi <a :href="prepareEmailLink(EMAIL_REPORT, `Segnalazione errore: ${venue.name} (identificativo: ${venue.id})`)">segnalare questa attività</a>.</p>
						</div>
					</div>
				</div>
			</div>

			<pg-lightbox
				v-if="lightboxOpen"
				:title="venue.name"
				:images="lightboxImages"
				:index="lightboxIndex"
				:arrows="$mq.comfortable"
				:thumbnails="$mq.comfortable"
				@close="closeLightbox">
			</pg-lightbox>
		</template>

		<pg-page-footer />
	</div>
</template>