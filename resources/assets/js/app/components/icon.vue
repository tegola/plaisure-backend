<template>
	<svg :class="['icon', 'icon-' + icon, spinning ? 'icon-spinning': '']"><use v-bind:xlink:href="'#icon-' + icon"></use></svg>
</template>

<script>
	export default {
		props: ['icon', 'spinning']
	}
</script>
