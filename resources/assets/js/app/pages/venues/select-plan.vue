<script>
import venueFormStore from 'prontogioco/app/store/venue-form';

export default {
	name: 'PgSelectVenuePlanPage',

	props: {
		venueId: {
			type: [String, Number],
			default: null
		}
	},

	data() {
		return {
			loading: false,
			error: false
		};
	},

	computed: {
		storeName() {
			return `venueForm/${this.venueId}`;
		},

		venue() {
			return this.$store.state[this.storeName].venue;
		}
	},

	meta() {
		return {
			title: this.venue && this.venue.id ? this.$t('pages.venue_form.title.edit') : this.$t('pages.venue_form.title.add')
		};
	},

	created() {
		// Register store
		if (!this.$store.state[this.storeName]) {
			this.$store.registerModule(this.storeName, venueFormStore);
			this.$store.commit(`${this.storeName}/setVenueId`, this.venueId);
		}
	},

	mounted() {
		// Load venue if it isn't already loaded
		if (!this.venue) this.loadData();
	},

	methods: {
		loadData() {
			this.error = false;
			this.loading = true;

			this.$store.dispatch(`${this.storeName}/load`)
				.catch(() => {
					this.error = true;
				})
				.then(() => {
					this.loading = false;
				});
		}
	}
};
</script>

<template>
	<div class="pg-select-venue-plan-page">
		<pg-navbar variant="dark" />

		<div v-if="loading || error" class="container d-flex text-muted text-center" style="height: 50vh">
			<div class="m-auto">
				<template v-if="loading">
					<pg-icon icon="circle-outline-notch" spinning />
					<h5 class="m-0">{{ $t('common.status.loading') }}&hellip;</h5>
				</template>
				<h4 v-if="error" class="text-danger mb-0">C'è stato un errore nel caricamento dei dati</h4>
			</div>
		</div>

		<div v-if="!loading && venue">
			<div class="container">
				<div class="row">
					<div class="col-lg-9 mx-lg-auto">
						Contenuto qui:<br>

						<router-link :to="{ name: 'venues.edit', params: { venueId }}">{{ venue.name }}</router-link>
					</div>
				</div>

				<div class="card-group">
					<div class="card">
						<div class="card-body">
							<p class="card-text mb-1 initialism text-nowrap">Per iniziare</p>
							<h3 class="card-title">Gratuito</h3>
							<p class="card-text lead">€0,00/mese</p>
						</div>
						<hr class="my-0">
						<div class="card-body">
							<ul class="mb-0">
								<li>Posizionamento normale nei risultati di ricerca</li>
								<li>Puoi caricare fino a <strong>5</strong> foto</li>
								<li>Mostra le attività vicine</li>
							</ul>
						</div>
					</div>

					<div class="card">
						<div class="card-body">
							<p class="card-text mb-1 initialism text-nowrap">Il preferito</p>
							<h3 class="card-title text-primary">Premium 1</h3>
							<p class="card-text lead">€39,00/mese</p>
						</div>
						<hr class="my-0">
						<div class="card-body">
							<ul class="mb-0">
								<li>Bonus di 5km nel posizionamento nei risultati di ricerca</li>
								<li>Mostra fino a <strong>20</strong> foto</li>
								<li>Nasconde le attività vicine</li>
							</ul>
						</div>
					</div>

					<div class="card">
						<div class="card-body">
							<p class="card-text mb-1 initialism text-nowrap">Da professionisti</p>
							<h3 class="card-title">Premium 2</h3>
							<p class="card-text lead">€59,00/mese</p>
						</div>
						<hr class="my-0">
						<div class="card-body">
							<ul class="mb-0">
								<li>Bonus di 10km nel posizionamento nei risultati di ricerca</li>
								<li>Puoi caricare fino a <strong>50</strong> foto</li>
								<li>Nasconde le attività vicine</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

		<pg-page-footer />
	</div>
</template>
