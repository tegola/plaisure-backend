<script>
import { APP_LOCALE } from 'prontogioco/constants';
import PgButton from './button';

export default {
	name: 'PgSubscriptionCard',

	components: {
		PgButton
	},

	props: {
		subscription: {
			type: Object,
			required: true
		},
		currentSubscription: {
			type: Object,
			default: null
		},
		selectedSubscription: {
			type: Object,
			default: null
		},
		highlight: {
			type: String,
			default: null
		}
	},

	computed: {
		isCurrent() {
			if (this.subscription.name == 'default') {
				// No subscription or subscription with an end date
				return !this.currentSubscription || this.currentSubscription.ends_at;
			} else {
				// Same subscription, which is current only if still active (no
				// end date)
				return this.currentSubscription
					&& this.currentSubscription.name == this.subscription.name
					&& !this.currentSubscription.ends_at;
			}
		},

		isSelected() {
			return this.selectedSubscription && this.selectedSubscription.name == this.subscription.name;
		},

		isOnGracePeriod() {
			return this.currentSubscription
				&& this.currentSubscription.name == this.subscription.name
				&& this.currentSubscription.ends_at;
		},

		price() {
			// FIXME: usare vue i18n number formatter
			return this.subscription.price.toFixed(2).replace('.', ',');
		},

		endDate() {
			const endDate = this.isOnGracePeriod ? this.currentSubscription.ends_at.date : null;

			// No end date available
			if (!endDate) return null;

			// FIXME: usare i18n date formatter
			const localeAdjusted = APP_LOCALE.replace('_', '-');

			return new Date(endDate).toLocaleDateString(localeAdjusted, {
				day: 'numeric',
				month: '2-digit',
				year: 'numeric'
			});
		},

		highlightText() {
			return this.highlight || this.subscription.highlight;
		},

		buttonProps() {
			if (this.isCurrent) {
				return {
					disabled: true,
					variant: 'neutral',
					vText: 'Piano corrente'
				};
			} else if (this.isSelected) {
				return {
					variant: 'secondary',
					vText: 'Cambia'
				};
			} else {
				return {
					variant: 'primary',
					vText: 'Scegli'
				};
			}
		}
	}
};
</script>

<template>
	<div class="card">
		<div class="card-body d-flex flex-column">
			<div>
				<span v-if="highlightText" class="badge badge-primary initialism">{{ highlightText }}</span>
				<h3 class="card-title">{{ $t(`db.subscriptions.${subscription.name}`) }}</h3>
				<p class="card-text lead">€ {{ price }}/mese</p>
				<p v-if="endDate" class="text-info">Attivo fino al {{ endDate }}</p>
				<ul class="list-unstyled">
					<li v-for="(line, index) in subscription.lines" :key="index">{{ line }}</li>
				</ul>
			</div>
			<div class="mt-auto text-center">
				<pg-button
					v-bind="buttonProps"
					block
					class="mt-auto"
					@click="$emit('select')"
					v-text="buttonProps.vText"
				/>
			</div>
		</div>
	</div>
</template>