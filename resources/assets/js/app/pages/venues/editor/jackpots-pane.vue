<script>
import BInput from 'bootstrap-vue/es/components/form-input/form-input';

export default {
	name: 'PgVenueEditorJackpotsPane',

	components: {
		BInput
	},

	props: {
		venue: {
			type: Object,
			required: true
		}
	}
}
</script>

<template>
	<div>
		<h4>Jackpot</h4>
		<hr>
		<div class="row">
			<div class="col-md-4" v-for="n in [1, 2, 3]" :key="n">
				<div class="form-group">
					<label>Jackpot {{ n }}</label>
					<b-input placeholder="Nome" v-model="venue.jackpots[n].label" />
				</div>
				<div class="form-group">
					<b-input type="number" placeholder="Valore" v-model.number="venue.jackpots[n].value" step="0.01" />
				</div>
			</div>
		</div>
	</div>
</template>