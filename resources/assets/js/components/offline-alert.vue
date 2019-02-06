<script>
import BAlert from 'bootstrap-vue/es/components/alert/alert';

const onlineEvents = ['online', 'offline', 'load'];

export default {
	name: 'PgOfflineAlert',

	components: {
		BAlert
	},

	data() {
		return {
			online: navigator.onLine || false
		};
	},

	mounted() {
		onlineEvents.forEach(event => window.addEventListener(event, this.updateOnlineStatus));
	},

	beforeDestroy() {
		onlineEvents.forEach(event => window.removeEventListener(event, this.updateOnlineStatus));
	},

	methods: {
		updateOnlineStatus() {
			this.online = navigator.onLine || false;
		}
	}
};
</script>

<template>
	<transition appear>
		<b-alert v-if="!online" show variant="danger" class="pg-offline-alert">
			{{ $t('components.offline_alert.offline') }}
		</b-alert>
	</transition>
</template>