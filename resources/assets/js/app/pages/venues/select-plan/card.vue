<script>
import { APP_LOCALE } from 'prontogioco/constants';
import PgButton from 'prontogioco/app/components/button';

export default {
	name: 'PgSelectPlanPageSubscription',

	components: {
		PgButton
	},

	props: {
		subscription: {
			type: Object,
			required: true
		},
		current: {
			type: Boolean,
			default: false
		},
		selected: {
			type: Boolean,
			default: false
		},
		endDate: {
			type: String,
			default: null
		}
	},

	computed: {
		formattedPrice() {
			// FIXME: usare vue i18n number formatter
			return this.subscription.price.toFixed(2).replace('.', ',');
		},

		formattedEndDate() {
			// FIXME: usare i18n date formatter
			const localeAdjusted = APP_LOCALE.replace('_', '-');

			return new Date(this.endDate).toLocaleDateString(localeAdjusted, {
				day: 'numeric',
				month: '2-digit',
				year: 'numeric'
			});
		}
	}
};
</script>

<template>
	<div class="card">
		<div class="card-body d-flex flex-column">
			<div>
				<span v-if="subscription.highlight" class="badge badge-primary initialism">Il più acquistato</span>
				<h3 class="card-title">{{ subscription.name }}</h3>
				<p class="card-text lead">€ {{ formattedPrice }}/mese</p>
				<ul>
					<li v-for="(line, index) in subscription.lines" :key="index">{{ line }}</li>
				</ul>
			</div>
			<div class="mt-auto text-center">
				<pg-button
					:disabled="current || selected"
					:variant="current ? 'outline-neutral' : 'primary'"
					block
					class="mt-auto"
					@click="$emit('select')">
					{{ current ? 'Piano attuale' : 'Scegli' }}
				</pg-button>
				<div class="mt-2 small">
					<template v-if="current && endDate">Rinnovo: {{ formattedEndDate }}</template>
					<template v-else>&nbsp;</template>
				</div>
			</div>
		</div>
	</div>
</template>