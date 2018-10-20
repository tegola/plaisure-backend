<script>
export default {
	name: 'PgErrorPage',

	props: {
		code: {
			type: Number,
			default: 404
		},
		message: {
			type: String,
			default: function() {
				let key;

				switch (this.code) {
					case 404: key = 'pages.error.not_found'; break;
					case 500: key = 'pages.error.server_error'; break;
					default: key = 'pages.error.not_found';
				}

				return this.$t(key);
			}
		}
	},

	meta() {
		return {
			title: this.message
		};
	}
};
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