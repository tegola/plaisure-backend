<script>
import { mapState } from 'vuex';
import PgButton from 'prontogioco/app/components/button';

export default {
	name: 'PgUserVenuesPage',

	components: {
		PgButton
	},

	computed: {
		...mapState('user', ['venues'])
	},

	methods: {
		venueCategoryString(venue) {
			if (!venue.categories || !venue.categories.length) return null;

			return venue.categories[0].name;
		}
	}
};
</script>

<template>
	<div>
		<pg-navbar variant="dark" />

		<div class="container my-5">
			<div class="d-flex justify-content-between align-items-center">
				<h2>Gestisci le tue attività</h2>
				<pg-button :to="{ name: 'venues.add' }" variant="primary" icon="plus">Aggiungi</pg-button>
			</div>
			<hr>

			<div class="row">
				<div v-for="venue in venues" :key="venue.id" class="col-md-6 col-xl-4 mb-4">
					<router-link :to="{ name: 'venues.edit', params: { venueId: venue.id } }" class="h-100">
						<div class="card h-100 flex-row">
							<div
								v-if="venue.photos && venue.photos[0]"
								:style="{ background: `url(${venue.photos[0].thumbnail_url})`, backgroundSize: 'cover', backgroundPosition: 'center center', width: '160px' }"
								class="embed-responsive embed-responsive-2by3"
							/>
							<div
								v-else
								class="embed-responsive embed-responsive-2by3 bg-light d-flex justify-content-center align-items-center"
								style="width: 160px;"
							/>

							<div class="card-body d-flex flex-column justify-content-center">
								<h4 class="card-title font-weight-bold">{{ venue.name }}</h4>
								<div class="card-subtitle mb-2 text-muted initialism">{{ venueCategoryString(venue) }}</div>
								<p class="card-text">{{ venue.address.short }}</p>
							</div>
						</div>
					</router-link>
				</div>

				<!--
				<div class="col-md-6 col-xl-4">
					<router-link :to="{ name: 'venues.add' }" class="d-block h-100">
						<div class="card h-100 flex-row">
							<div class="embed-responsive embed-responsive-2by3 bg-light d-flex justify-content-center align-items-center" style="width: 160px;">
								<pg-icon icon="plus"></pg-icon>
							</div>
							<div class="card-body d-flex flex-column justify-content-center align-items-center">
								<h4 class="card-text">Aggiungi un'altra attività</h4>
							</div>
						</div>
					</router-link>
				</div>
				-->
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>