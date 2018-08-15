<script>
import { mapState, mapGetters } from 'vuex';
import _extend from 'lodash/extend';
import constants from 'prontogioco/constants';

import PgLightbox from 'prontogioco/app/components/lightbox';
import PgVenueDetailPageContactCard from './contact-card';
import store from 'prontogioco/app/store';

const handleRoute = function(to, from, next) {
	store.dispatch('venueDetail/load', to.params.venueId)
		.then(() => {
			next();
		})
		.catch(() => {
			next({
				name: 'error',
				params: [to.path],
				replace: true
			});
		});
};

export default {
	name: 'PgVenueDetailPage',

	components: {
		PgLightbox,
		PgVenueDetailPageContactCard
	},

	filters: {
		formatCurrency(number) {
			return number.toLocaleString(undefined, {
				style: 'currency',
				currency: 'EUR',
				minimumFractionDigits: 2
			});
		}
	},

	props: {
		venueId: {
			type: String,
			required: true
		}
	},

	data() {
		return {
			loading: false,
			lightboxIndex: 0,
			lightboxOpen: false,
			hoursExpanded: false
		};
	},

	meta() {
		const venue = this.venue;
		const structuredData = this.$store.state.venueDetail.structuredData;
		let metadata = {};

		if (venue) {
			// Title
			metadata.title = `${venue.name} - ${this.subtitle}`;

			// Description
			if (venue.description) {
				metadata.meta = [
					{
						vmid: 'description',
						name: 'description',
						content: venue.description
					}
				];
			}
		}

		// Structured data
		if (structuredData) {
			metadata.script = [
				{ type: 'application/ld+json', innerHTML: JSON.stringify(structuredData) }
			];
		}

		return metadata;
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
			const categories = this.venue.categories;
			const city = this.venue.address.city;

			if (categories.length) {
				return this.$t('pages.venue_detail.subtitle', {
					category: this.$t(`db.categories.${categories[0].machine_name}`),
					city
				});
			} else {
				return city;
			}
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

	beforeRouteEnter: handleRoute,

	beforeRouteUpdate: handleRoute,

	beforeCreate() {
		_extend(this, constants);
	},

	methods: {
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
			return `mailto:${address}?subject=${encodeURIComponent(subject)}`;
		}
	}
};
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
					<div ref="gallery" class="header-gallery">
						<div class="header-gallery-bg">
							<div v-for="i in 6" :key="i" class="header-photo header-photo-placeholder" />
						</div>
						<router-link v-if="!venue.has_owner" to="/promote" class="header-photo header-photo-add">
							<pg-icon icon="plus" />
							{{ $t('pages.venue_detail.gallery.add') }}
						</router-link>
						<template v-for="(file, index) in venue.photos">
							<a v-if="index < 10" :href="file.resized_url" :key="index" class="header-photo" @click.prevent="showLightbox(index)">
								<div
									:style="'background-image: url(' + file.thumbnail_url + ')'"
									class="embed-responsive embed-responsive-1by1 header-photo-img"
								/>
							</a>
							<a v-if="index == 10" :href="file.resized_url" :key="index" class="header-photo" @click.prevent="showLightbox(index)">
								<div :style="'background-image: url(' + file.thumbnail_url + ')'" class="embed-responsive embed-responsive-1by1 header-photo-img">
									<div class="header-photo-zoom">
										<pg-icon icon="search" class="mb-1" />
										{{ $t('pages.venue_detail.gallery.all') }}
									</div>
								</div>
							</a>
						</template>
					</div>

					<!-- Title -->
					<h2 class="header-title">{{ venue.name }}</h2>
					<ul class="list-inline header-subtitle">
						<li class="list-inline-item">{{ subtitle }}</li>
						<li v-if="venue.business_hours.length" class="list-inline-item">
							<span v-if="isOpen" class="text-success">{{ $t('pages.venue_detail.card.open_now') }}</span>
							<strong v-else class="text-danger">{{ $t('pages.venue_detail.card.closed_now') }}</strong>
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
								<div v-for="(jackpot, index) in venue.jackpots" :key="index" class="col-md-4">
									<div :class="['jackpot', index < 3 ? 'mb-3 mb-md-0' : null]">
										<img :src="`/img/detail/jackpot-${index}.svg`" class="jackpot-icon">
										<div>
											<div class="jackpot-name">{{ jackpot.label && jackpot.value ? jackpot.label : `Jackpot ${index}` }}</div>
											<div class="jackpot-value">{{ jackpot.value | formatCurrency }}</div>
											<div v-if="!venue.has_owner"><router-link to="/promote">{{ $t('pages.venue_detail.common.edit') }}</router-link></div>
										</div>
									</div>
								</div>
							</div>
							<hr>
						</template>

						<!-- Description -->
						<template v-if="venue.description">
							<div class="my-5">
								<h4>{{ $t('pages.venue_detail.description') }}</h4>
								<p>{{ venue.description }}</p>
							</div>
							<hr>
						</template>

						<!-- Details -->
						<div class="my-5">
							<h4>
								{{ $t('pages.venue_detail.details.title') }}
								<router-link v-if="!venue.has_owner" to="/promote" class="small ml-2">{{ $t('pages.venue_detail.common.edit') }}</router-link>
							</h4>
							<div class="row">
								<div class="col-md">
									<ul class="list-unstyled mb-0 mb-md-3">
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.details.concessionaire') }}:
											<strong v-if="venue.concessionaire">{{ venue.concessionaire.name }}</strong>
											<span v-else class="text-muted">{{ $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.details.surface_size') }}:
											<strong v-if="venue.surface_size">{{ venue.surface_size }} mq.</strong>
											<span v-else class="text-muted">{{ $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.details.vlt_machine_count') }}:
											<strong v-if="venue.vlt_machine_count">{{ venue.vlt_machine_count }}</strong>
											<span v-else class="text-muted">{{ $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.details.vlt_platforms') }}:
											<strong v-if="venue.vlt_platforms.length">{{ vltPlatformNames }}</strong>
											<span v-else class="text-muted">{{ $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.details.awp_machine_count') }}:
											<strong v-if="venue.awp_machine_count">{{ venue.awp_machine_count }}</strong>
											<span v-else class="text-muted">{{ $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.details.arcade_roulette') }}:
											<strong v-if="venue.arcade_roulette" class="text-success">{{ $t('pages.venue_detail.common.yes') }}</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? $t('pages.venue_detail.common.no') : $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.details.online_casino') }}:
											<a v-if="venue.urls.online_casino" :href="venue.urls.online_casino" target="_blank">{{ venue.urls.online_casino }}</a>
											<span v-else class="text-muted">{{ $t('pages.venue_detail.common.unknown') }}</span>
										</li>
									</ul>
								</div>
								<div class="col-md">
									<ul class="list-unstyled">
										<template v-if="isInCategory('betting_agency')">
											<li class="detail-list-item">
												{{ $t('pages.venue_detail.details.sports_betting') }}:
												<strong v-if="venue.sports_betting" class="text-success">{{ $t('pages.venue_detail.common.yes') }}</strong>
												<span v-else class="text-muted">{{ venue.has_owner ? $t('pages.venue_detail.common.no') : $t('pages.venue_detail.common.unknown') }}</span>
											</li>
											<li class="detail-list-item">
												{{ $t('pages.venue_detail.details.virtual_betting') }}:
												<strong v-if="venue.virtual_betting" class="text-success">{{ $t('pages.venue_detail.common.yes') }}</strong>
												<span v-else class="text-muted">{{ venue.has_owner ? $t('pages.venue_detail.common.no') : $t('pages.venue_detail.common.unknown') }}</span>
											</li>
											<li class="detail-list-item">
												{{ $t('pages.venue_detail.details.horse_betting') }}:
												<strong v-if="venue.horse_betting" class="text-success">{{ $t('pages.venue_detail.common.yes') }}</strong>
												<span v-else class="text-muted">{{ venue.has_owner ? $t('pages.venue_detail.common.no') : $t('pages.venue_detail.common.unknown') }}</span>
											</li>
										</template>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.details.parking_capacity') }}:
											<strong v-if="venue.parking_capacity">{{ venue.parking_capacity }}</strong>
											<span v-else class="text-muted">{{ $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.details.pay_per_view_platforms') }}:
											<strong v-if="venue.pay_per_view_platforms.length">{{ payPerViewPlatformNames }}</strong>
											<span v-else class="text-muted">{{ $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li v-if="isInCategory('betting_agency')" class="detail-list-item">
											{{ $t('pages.venue_detail.details.seating_capacity') }}:
											<strong v-if="venue.seating_capacity">{{ venue.seating_capacity }}</strong>
											<span v-else class="text-muted">{{ $t('pages.venue_detail.common.unknown') }}</span>
										</li>
									</ul>
								</div>
							</div>
						</div>

						<hr>

						<!-- Amenities -->
						<div class="my-5">
							<h4>
								{{ $t('pages.venue_detail.amenities.title') }}
								<router-link v-if="!venue.has_owner" to="/promote" class="small ml-2">{{ $t('pages.venue_detail.common.edit') }}</router-link>
							</h4>
							<div class="row">
								<div class="col-md">
									<ul class="list-unstyled mb-0 mb-md-3">
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.amenities.atm') }}:
											<strong v-if="venue.amenities.atm" class="text-success">{{ $t('pages.venue_detail.common.yes') }}</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? $t('pages.venue_detail.common.no') : $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.amenities.bar') }}:
											<strong v-if="venue.amenities.bar" class="text-success">{{ $t('pages.venue_detail.common.yes') }}</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? $t('pages.venue_detail.common.no') : $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.amenities.pay_per_view') }}:
											<strong v-if="venue.amenities.pay_per_view" class="text-success">{{ $t('pages.venue_detail.common.yes') }}</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? $t('pages.venue_detail.common.no') : $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.amenities.pos') }}:
											<strong v-if="venue.amenities.pos" class="text-success">{{ $t('pages.venue_detail.common.yes') }}</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? $t('pages.venue_detail.common.no') : $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.amenities.private_parking') }}:
											<strong v-if="venue.amenities.private_parking" class="text-success">{{ $t('pages.venue_detail.common.yes') }}</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? $t('pages.venue_detail.common.no') : $t('pages.venue_detail.common.unknown') }}</span>
										</li>
									</ul>
								</div>
								<div class="col-md">
									<ul class="list-unstyled">
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.amenities.restaurant') }}:
											<strong v-if="venue.amenities.restaurant" class="text-success">{{ $t('pages.venue_detail.common.yes') }}</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? $t('pages.venue_detail.common.no') : $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.amenities.security') }}:
											<strong v-if="venue.amenities.security" class="text-success">{{ $t('pages.venue_detail.common.yes') }}</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? $t('pages.venue_detail.common.no') : $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.amenities.smoking_area') }}:
											<strong v-if="venue.amenities.smoking_area" class="text-success">{{ $t('pages.venue_detail.common.yes') }}</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? $t('pages.venue_detail.common.no') : $t('pages.venue_detail.common.unknown') }}</span>
										</li>
										<li class="detail-list-item">
											{{ $t('pages.venue_detail.amenities.wifi') }}:
											<strong v-if="venue.amenities.wifi" class="text-success">{{ $t('pages.venue_detail.common.yes') }}</strong>
											<span v-else class="text-muted">{{ venue.has_owner ? $t('pages.venue_detail.common.no') : $t('pages.venue_detail.common.unknown') }}</span>
										</li>
									</ul>
								</div>
							</div>
						</div>

						<!-- Promote -->
						<div class="card bg-light my-4 text-center">
							<div class="card-body">
								<h4 class="card-title">{{ $t('pages.venue_detail.claim.title') }}</h4>
								<p class="card-text">{{ $t('pages.venue_detail.claim.intro') }} <router-link to="/promote">{{ $t('pages.venue_detail.claim.more') }}&hellip;</router-link></p>
								<p class="card-text"><a :href="prepareEmailLink(EMAIL_VENUES, $t('pages.venue_detail.claim.subject', { name: venue.name, id: venue.id }))" class="btn btn-primary">{{ $t('pages.venue_detail.claim.action') }}</a></p>
							</div>
						</div>
					</div>
					<div class="col-lg-4">

						<!-- Contact card for big screens -->
						<pg-venue-detail-page-contact-card class="d-none d-lg-block" />

						<!-- Nearby venues -->
						<div v-if="nearbyVenues.length" class="my-5">
							<h5 class="mb-3">{{ $t('pages.venue_detail.nearby') }}</h5>
							<ul class="list-unstyled">
								<li v-for="nearbyVenue in nearbyVenues" :key="nearbyVenue.id" class="d-flex align-items-start">
									<img :src="`/img/map/pin-normal-${nearbyVenue.categories[0].machine_name || 'collapsed'}.svg`" class="mr-3">
									<p>
										<strong><router-link :to="`/venues/${nearbyVenue.id}`">{{ nearbyVenue.name }}</router-link></strong><br>
										<span class="initialism text-muted">{{ $t(`db.categories.${nearbyVenue.categories[0].machine_name}`) }}</span><br>
										{{ nearbyVenue.address.short }}
									</p>
								</li>
							</ul>
						</div>

						<!-- Report -->
						<div class="my-4">
							<h5>{{ $t('pages.venue_detail.issues.title') }}</h5>
							<i18n tag="p" path="pages.venue_detail.issues.intro">
								<a :href="prepareEmailLink(EMAIL_REPORT, $t('pages.venue_detail.issues.subject', { name: venue.name, id: venue.id }))" place="report">{{ $t('pages.venue_detail.issues.report') }}</a>
							</i18n>
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
				@close="closeLightbox"
			/>
		</template>

		<pg-page-footer />
	</div>
</template>