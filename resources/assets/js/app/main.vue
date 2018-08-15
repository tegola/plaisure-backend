<script>
import BAlert from 'bootstrap-vue/es/components/alert/alert';

const onlineEvents = ['online', 'offline', 'load'];

export default {
	name: 'PgApp',

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
	<div>
		<transition appear>
			<b-alert v-if="!online" show variant="danger" class="pg-offline-alert">
				{{ $t('common.status.offline_warning') }}
			</b-alert>
		</transition>
		<router-view :key="$route.path" />
	</div>
</template>