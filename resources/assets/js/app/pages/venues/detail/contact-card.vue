<script>
import { mapState, mapGetters } from 'vuex';
import { Map as PgMap, Marker as PgMapMarker } from 'vue2-google-maps';

export default {
	name: 'PgVenueDetailPageContactCard',

	components: {
		PgMap,
		PgMapMarker
	},

	data() {
		return {
			mapOptions: {
				disableDefaultUI: true,
				draggable: false,
				scrollwheel: false,
				styles: [
					{ // No labels on POI
						'featureType': 'poi',
						'elementType': 'labels.text',
						'stylers': [{ 'visibility': 'off' }]
					}
				]
			},
			hoursExpanded: false
		}
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
}
</script>

<template>
	<div class="card contact-card">
		<!-- Map -->
		<div class="embed-responsive embed-responsive-16by9 contact-card-map">
			<pg-map class="embed-responsive-item" :center="venue.coords" :zoom="15" :options="mapOptions">
				<pg-map-marker
					:position="venue.coords"
					:icon="`/img/map/pin-normal-${venue.categories[0].machine_name}.svg`"
				/>
			</pg-map>
		</div>
		
		<div class="list-group list-group-flush">
			<!-- Address -->
			<div class="list-group-item contact-card-list-item">
				<router-link v-if="!venue.has_owner" to="/promote" class="float-right">{{ $t('pages.detail.common.edit') }}</router-link>

				<pg-icon icon="directions" class="contact-card-list-item-icon" />
				<p class="mb-2">
					<strong>{{ venue.name }}</strong>
					<div v-for="(line, index) in addressLines" :key="index">{{ line }}</div>
				</p>
				<p class="mb-0"><a :href="googleMapsUrl" target="_blank">{{ $t('pages.detail.card.directions') }}</a></p>
			</div>

			<!-- Business hours -->
			<div class="list-group-item contact-card-list-item">
				<router-link v-if="!venue.has_owner" to="/promote" class="float-right">{{ $t('pages.detail.common.edit') }}</router-link>
				<pg-icon icon="clock-outline" :class="['contact-card-list-item-icon', venue.business_hours.length ? null : 'text-muted']" />

				<template v-if="venue.has_owner && venue.business_hours.length">
					<a href="#" :class="isOpen ? 'text-success' : 'text-danger'" @click.prevent="toggleHours">
						{{ isOpen ? $t('pages.detail.card.open_now') : $t('pages.detail.card.closed_now') }}<pg-icon :icon="hoursExpanded ? 'chevron-up' : 'chevron-down'" class="ml-1 contact-card-chevron-icon" />
					</a>
					<table v-if="hoursExpanded" v-cloak>
						<tr v-for="row in businessHoursRows">
							<td class="align-top pr-3">{{ row.day }}</td>
							<td>
								<div v-if="!row.hours.length" class="text-muted">{{ $t('pages.detail.card.closed') }}</div>
								<div v-if="row.hours.length == 2">{{ row.hours[0] }}&ndash;{{ row.hours[1] }}</div>
								<div v-if="row.hours.length == 4">{{ row.hours[2] }}&ndash;{{ row.hours[3] }}</div>
							</td>
						</tr>
					</table>
				</template>
				<p class="mb-0 text-muted" v-else>{{ $t('pages.detail.card.no_hours') }}</p>
			</div>

			<!-- Contacts -->
			<div class="list-group-item contact-card-list-item">
				<router-link v-if="!venue.has_owner" to="/promote" class="float-right">{{ $t('pages.detail.common.edit') }}</router-link>
				<pg-icon icon="phone" :class="['contact-card-list-item-icon', hasContacts ? null : 'text-muted']" />

				<ul class="list-unstyled mb-0" v-if="hasContacts">
					<li v-if="venue.contacts.phone"><a :href="`tel://${venue.contacts.phone}`">{{ venue.contacts.phone }}</a></li>
					<li v-if="venue.contacts.email"><a :href="`mailto:${venue.contacts.email}`">{{ venue.contacts.email }}</a></li>
					<li v-if="venue.contacts.facebook"><a :href="facebookMessengerUrl" target="_blank">{{ venue.contacts.facebook }}</a> <span class="text-muted">(Facebook Messenger)</span></li>
					<li v-if="venue.contacts.twitter"><a :href="twitterUrl" target="_blank">@{{ venue.contacts.twitter }}</a> <span class="text-muted">(Twitter)</span></li>
				</ul>
				<p class="mb-0 text-muted" v-else>{{ $t('pages.detail.card.no_contacts') }}</p>
			</div>

			<!-- URLs -->
			<div class="list-group-item contact-card-list-item">
				<router-link v-if="!venue.has_owner" to="/promote" class="float-right">{{ $t('pages.detail.common.edit') }}</router-link>
				<pg-icon icon="globe" :class="['contact-card-list-item-icon', hasUrls ? null : 'text-muted']" />
				<ul class="list-unstyled mb-0" v-if="hasUrls">
					<li v-if="venue.urls.site"><a :href="venue.urls.site" target="_blank">{{ readableSiteUrl }}</a></li>
					<li v-if="venue.urls.facebook"><a :href="venue.urls.facebook" target="_blank">(Facebook)</a></li>
				</ul>
				<p class="mb-0 text-muted" v-else>{{ $t('pages.detail.card.no_urls') }}</p>
			</div>
		</div>
	</div>
</template>