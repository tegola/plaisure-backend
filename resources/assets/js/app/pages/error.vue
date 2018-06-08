<script>
import setTitle from 'prontogioco/utilities/set-title';

export default {
	name: 'PgErrorPage',

	props: {
		code: {
			type: Number,
			default: 404
		},
		message: {
			type: String,
			default: 'Pagina non trovata'
		}
	},

	mounted() {
		setTitle(this.message);
	}
}
</script>

<template>
	<div>
		<pg-navbar />

		<div class="container my-5 text-center">
			<h1 class="display-1 font-weight-bold">{{ code }}</h1>
			<h3>{{ message }}</h3>
		</div>

		<pg-page-footer />
	</div>
</template>