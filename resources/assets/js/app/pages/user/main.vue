<script>
import { mapState } from 'vuex';
import PgNoItems from 'prontogioco/app/components/no-items';
import PgButton from 'prontogioco/app/components/button';

export default {
	name: 'PgUserPage',

	components: {
		PgNoItems,
		PgButton
	},

	computed: {
		...mapState('user', ['user', 'venues'])
	},

	meta() {
		return {
			title: this.$t('pages.user.meta_title')
		};
	},

	methods: {
		venueCategoryString(venue) {
			if (!venue.categories || !venue.categories.length) return null;

			return this.$t(`db.categories.${venue.categories[0].machine_name}`);
		},

		logout() {
			this.$store.dispatch('user/logout')
				.then(() => {
					this.$router.push({ name: 'home' });
				});
		}
	}
};
</script>

<template>
	<div class="pg-user-page">
		<pg-navbar variant="dark" />

		<div class="container my-5">
			<div class="d-flex justify-items-between">
				<div>
					<h3>{{ $t('pages.user.title', { name: user.name }) }}</h3>
				</div>
				<div class="ml-auto">
					<pg-button to="/venues/add" variant="primary" icon="plus" class="mr-3">{{ $t('pages.user.actions.add_venue') }}</pg-button>
					<pg-button to="/user/edit">{{ $t('pages.user.actions.edit_profile') }}</pg-button>
					<pg-button @click="logout">{{ $t('pages.user.actions.logout') }}</pg-button>
				</div>
			</div>

			<div v-if="venues.length" class="row mt-5">
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
			</div>

			<pg-no-items v-if="!venues.length" :title="$t('pages.user.no_items.title')" class="py-5 my-5">
				<i18n path="pages.user.no_items.message">
					<router-link :to="{ name: 'venues.add' }" place="action">
						<strong>{{ $t('pages.user.no_items.message_action') }}</strong>
					</router-link>
				</i18n>
			</pg-no-items>
		</div>
		<pg-page-footer />
	</div>
</template>