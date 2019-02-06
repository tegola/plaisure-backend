<script>
import { mapState } from 'vuex';
import PgButton from '@/components/button';
import PgNoItems from '@/components/no-items';
import PgVenueGridItem from '@/components/venue-grid-item';

export default {
	name: 'PgUserDetailPage',

	components: {
		PgNoItems,
		PgButton,
		PgVenueGridItem
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

		<div v-if="user" class="container my-5">
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
					<router-link :to="{ name: 'venues.detail', params: { venueId: venue.id } }" class="text-inherit">
						<pg-venue-grid-item
							:venue="venue"
							:show-highlight="false"
							class="mb-2"
						/>
					</router-link>
					<pg-button
						:to="{ name: 'venues.edit', params: { venueId: venue.id } }"
						block
						variant="primary"
						size="sm">
						{{ $t('common.actions.edit') }}
					</pg-button>
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