<script>
import { mapState, mapGetters } from 'vuex';
import PgImageFrame from 'prontogioco/app/components/image-frame';
import { GOOGLE_MAPS_API_KEY } from 'prontogioco/constants';

export default {
	name: 'PgVenueDetailPageContactCard',

	components: {
		PgImageFrame
	},

	props: {
		showEditAction: {
			type: Boolean,
			default: false
		},
		editRoute: {
			type: [String, Object],
			default: null
		}
	},

	data() {
		return {
			mapOptions: {
				styles: [
					{ // No labels on POI
						'featureType': 'poi',
						'elementType': 'labels.text',
						'stylers': [{ 'visibility': 'off' }]
					}
				]
			},
			hoursExpanded: false
		};
	},

	computed: {
		...mapState('venueDetail', [
			'venue'
		]),

		...mapGetters('venueDetail', [
			'isOpen',
			'businessHoursRows',
			'hasContacts',
			'hasUrls',
			'googleMapsUrl',
			'facebookMessengerUrl',
			'twitterUrl',
			'readableSiteUrl'
		]),

		mapImgUrl() {
			return [
				'https://maps.googleapis.com/maps/api/staticmap',
				`?center=${this.venue.coords.lat},${this.venue.coords.lng}`,
				'&zoom=15',
				'&size=700x395',
				'&scale=2',
				'&style=feature:poi|element:labels.text|visibility:off',
				`&key=${GOOGLE_MAPS_API_KEY}`
			].join('');
		},

		addressLines() {
			const a = this.venue.address;
			return [
				`${a.street} ${a.number}`.trim(),
				a.city,
				`${a.postcode} ${a.province}`.trim()
			];
		}
	},

	methods: {
		toggleHours() {
			this.hoursExpanded = !this.hoursExpanded;
		}
	}
};
</script>

<template>
	<div class="card contact-card">
		<pg-image-frame
			:src="mapImgUrl"
			ratio="16:9"
			class="contact-card-map"
			content-class="contact-card-map-content">
			<img
				:src="`/img/map/pin-normal-${venue.categories[0].machine_name}.svg`"
				class="contact-card-map-marker">
		</pg-image-frame>

		<div class="list-group list-group-flush">
			<!-- Address -->
			<div class="list-group-item contact-card-list-item">
				<router-link v-if="showEditAction" :to="editRoute" class="float-right">{{ $t('pages.venue_detail.common.edit') }}</router-link>

				<pg-icon icon="directions" class="contact-card-list-item-icon" />
				<div class="mb-2">
					<strong>{{ venue.name }}</strong>
					<div v-for="(line, index) in addressLines" :key="index">{{ line }}</div>
				</div>
				<p class="mb-0"><a :href="googleMapsUrl" target="_blank">{{ $t('pages.venue_detail.card.directions') }}</a></p>
			</div>

			<!-- Business hours -->
			<div class="list-group-item contact-card-list-item">
				<router-link v-if="showEditAction" :to="editRoute" class="float-right">{{ $t('pages.venue_detail.common.edit') }}</router-link>
				<pg-icon :class="['contact-card-list-item-icon', venue.business_hours.length ? null : 'text-muted']" icon="clock-outline" />

				<template v-if="venue.has_owner && venue.business_hours.length">
					<a :class="isOpen ? 'text-success' : 'text-danger'" href="#" @click.prevent="toggleHours">
						{{ isOpen ? $t('pages.venue_detail.card.open_now') : $t('pages.venue_detail.card.closed_now') }}<pg-icon :icon="hoursExpanded ? 'chevron-up' : 'chevron-down'" class="ml-1 contact-card-chevron-icon" />
					</a>
					<table v-if="hoursExpanded" v-cloak>
						<tr v-for="row in businessHoursRows" :key="row.day">
							<td class="align-top pr-3">{{ row.day }}</td>
							<td>
								<div v-if="!row.hours.length" class="text-muted">{{ $t('pages.venue_detail.card.closed') }}</div>
								<div v-if="row.hours.length == 2">{{ row.hours[0] }}&ndash;{{ row.hours[1] }}</div>
								<div v-if="row.hours.length == 4">{{ row.hours[2] }}&ndash;{{ row.hours[3] }}</div>
							</td>
						</tr>
					</table>
				</template>
				<p v-else class="mb-0 text-muted">{{ $t('pages.venue_detail.card.no_hours') }}</p>
			</div>

			<!-- Contacts -->
			<div class="list-group-item contact-card-list-item">
				<router-link v-if="showEditAction" :to="editRoute" class="float-right">{{ $t('pages.venue_detail.common.edit') }}</router-link>
				<pg-icon :class="['contact-card-list-item-icon', hasContacts ? null : 'text-muted']" icon="phone" />

				<ul v-if="hasContacts" class="list-unstyled mb-0">
					<li v-if="venue.contacts.phone"><a :href="`tel://${venue.contacts.phone}`">{{ venue.contacts.phone }}</a></li>
					<li v-if="venue.contacts.email"><a :href="`mailto:${venue.contacts.email}`">{{ venue.contacts.email }}</a></li>
					<li v-if="venue.contacts.facebook"><a :href="facebookMessengerUrl" target="_blank">{{ venue.contacts.facebook }}</a> <span class="text-muted">(Facebook Messenger)</span></li>
					<li v-if="venue.contacts.twitter"><a :href="twitterUrl" target="_blank">@{{ venue.contacts.twitter }}</a> <span class="text-muted">(Twitter)</span></li>
				</ul>
				<p v-else class="mb-0 text-muted">{{ $t('pages.venue_detail.card.no_contact') }}</p>
			</div>

			<!-- URLs -->
			<div class="list-group-item contact-card-list-item">
				<router-link v-if="showEditAction" :to="editRoute" class="float-right">{{ $t('pages.venue_detail.common.edit') }}</router-link>
				<pg-icon :class="['contact-card-list-item-icon', hasUrls ? null : 'text-muted']" icon="globe" />
				<ul v-if="hasUrls" class="list-unstyled mb-0">
					<li v-if="venue.urls.site"><a :href="venue.urls.site" target="_blank">{{ readableSiteUrl }}</a></li>
					<li v-if="venue.urls.facebook"><a :href="venue.urls.facebook" target="_blank">Facebook</a></li>
				</ul>
				<p v-else class="mb-0 text-muted">{{ $t('pages.venue_detail.card.no_urls') }}</p>
			</div>
		</div>
	</div>
</template>