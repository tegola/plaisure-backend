<script>
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BInputGroup from 'bootstrap-vue/es/components/input-group/input-group';

export default {
	name: 'PgVenueEditorJackpotsPane',

	components: {
		BInput,
		BInputGroup
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
		<div class="form-row" v-for="n in [1, 2, 3]" :key="n">
			<label class="col-md-3 col-form-label text-md-right">Jackpot {{ n }}</label>
			<div class="col-md-9 col-lg-7">
				<div class="form-row">
					<div class="form-group col-md">
						<b-input placeholder="Nome" v-model="venue.jackpots[n].label"/>
					</div>
					<div class="form-group col-md">
						<b-input-group prepend="€">
							<b-input
								type="number"
								class="text-right"
								placeholder="Valore"
								v-model.number="venue.jackpots[n].value"
								step="0.01"
							/>
						</b-input-group>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>