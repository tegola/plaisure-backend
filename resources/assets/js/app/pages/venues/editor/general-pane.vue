<script>
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BTextarea from 'bootstrap-vue/es/components/form-textarea/form-textarea';
import BSelect from 'bootstrap-vue/es/components/form-select/form-select';
import BCheckbox from 'bootstrap-vue/es/components/form-checkbox/form-checkbox';
import BCheckboxGroup from 'bootstrap-vue/es/components/form-checkbox/form-checkbox-group';

export default {
	name: 'PgVenueEditorGeneralPane',

	components: {
		BInput,
		BTextarea,
		BSelect,
		BCheckbox,
		BCheckboxGroup
	},

	props: {
		venue: {
			type: Object,
			required: true
		},
		concessionaires: {
			type: Array,
			default: () => []
		},
		vltPlatforms: {
			type: Array,
			default: () => []
		},
		payPerViewPlatforms: {
			type: Array,
			default: () => []
		}
	}
}
</script>

<template>
	<div>
		<h4>Generale</h4>
		<hr>
		<div class="form-group">
			<label>Nome</label>
			<b-input v-model="venue.name" size="lg" />
		</div>
		<div class="form-group">
			<label>Descrizione</label>
			<b-textarea v-model="venue.description" rows="3" />
		</div>
		<div class="row">
			<div class="form-group col-md-4">
				<label>Concessionario</label>
				<b-select v-model="venue.concessionaire_id">
					<option :value="null">Non specificato</option>
					<option v-for="item in concessionaires" :value="item.id">{{ item.name }}</option>
				</b-select>
			</div>
			<div class="form-group col-6 col-md-4">
				<label>Superficie (mq.)</label>
				<b-input v-model.number="venue.surface_size" />
			</div>
			<div class="form-group col-6 col-md-4">
				<label>N. macchine VLT</label>
				<b-input v-model.number="venue.vlt_machine_count" />
			</div>
			<div class="form-group col-6 col-md-4">
				<label>N. macchine AWP</label>
				<b-input v-model.number="venue.awp_machine_count" />
			</div>
			<div class="form-group col-6 col-md-4">
				<label>Posti a sedere</label>
				<b-input v-model.number="venue.seating_capacity" />
			</div>
			<div class="form-group col-6 col-md-4">
				<label>Posti auto</label>
				<b-input v-model.number="venue.parking_capacity" />
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="form-group col-sm-6 col-md-3">
				<label>Piattaforme VLT</label>
				<b-checkbox-group v-model="venue.vlt_platform_ids" stacked>
					<b-checkbox v-for="item in vltPlatforms" :value="item.id" :key="item.id">
						{{ item.name}}
					</b-checkbox>
				</b-checkbox-group>
			</div>
			<div class="form-group col-sm-6 col-md-3">
				<label>Comodità</label>
				<div><b-checkbox v-model="venue.amenities.atm">Totem Bancomat</b-checkbox></div>
				<div><b-checkbox v-model="venue.amenities.bar">Bar</b-checkbox></div>
				<div><b-checkbox v-model="venue.amenities.pay_per_view">Pay per view</b-checkbox></div>
				<div><b-checkbox v-model="venue.amenities.pos">POS</b-checkbox></div>
				<div><b-checkbox v-model="venue.amenities.private_parking">Parcheggio privato</b-checkbox></div>
				<div><b-checkbox v-model="venue.amenities.restaurant">Ristorante</b-checkbox></div>
				<div><b-checkbox v-model="venue.amenities.security">Security</b-checkbox></div>
				<div><b-checkbox v-model="venue.amenities.smoking_area">Area fumatori</b-checkbox></div>
				<div><b-checkbox v-model="venue.amenities.wifi">Wi-Fi</b-checkbox></div>
			</div>
			<div class="form-group col-sm-6 col-md-3">
				<label>Piattaforme Pay per view</label>
				<b-checkbox-group v-model="venue.pay_per_view_platform_ids" stacked>
					<b-checkbox v-for="item in payPerViewPlatforms" :value="item.id" :key="item.id">
						{{ item.name}}
					</b-checkbox>
				</b-checkbox-group>
			</div>
			<div class="form-group col-sm-6 col-md-3">
				<label>Servizi</label>
				<div><b-checkbox v-model="venue.sports_betting">Scommesse sportive</b-checkbox></div>
				<div><b-checkbox v-model="venue.virtual_betting">Scommesse virtuali</b-checkbox></div>
				<div><b-checkbox v-model="venue.horse_betting">Scommesse ippiche</b-checkbox></div>
				<div><b-checkbox v-model="venue.arcade_roulette">Roulette arcade</b-checkbox></div>
			</div>
		</div>
	</div>
</template>