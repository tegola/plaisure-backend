<script>
import PgOfflineAlert from 'prontogioco/app/components/offline-alert';

export default {
	name: 'PgApp',

	components: {
		PgOfflineAlert
	}
};
</script>

<template>
	<div>
		<router-view :key="$route.path" />
		<pg-offline-alert />
	</div>
</template>