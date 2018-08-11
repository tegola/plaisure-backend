<script>
export default {
	name: 'PgApp'
};
</script>

<template>
	<router-view :key="$route.path" />
</template>