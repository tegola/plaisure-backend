<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BInputGroup from 'bootstrap-vue/es/components/input-group/input-group';

import formGroupProps from './form-group-props'

export default {
	name: 'PgVenueEditorGeneralPane',

	components: {
		BFormGroup,
		BInput,
		BInputGroup
	},

	props: {
		venue: {
			type: Object,
			required: true
		}
	},

	data() {
		return {
			formGroupProps
		};
	},

	computed: {
		$v() {
			return this.$parent.$v.venue;
		},
	}
}
</script>

<template>
	<div>
		<b-form-group
			label="Telefono"
			v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-md-7 col-lg-5">
					<b-input type="tel" v-model="venue.contacts.phone" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="E-mail"
			v-bind="formGroupProps"
			:state="!$v.contacts.email.$error"
			invalid-feedback="Inserisci un indirizzo email valido.">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input type="email" v-model="venue.contacts.email" placeholder="Es.: nome@gmail.com" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Facebook Messenger"
			v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-md-7 col-lg-5">
					<b-input v-model="venue.contacts.facebook" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Twitter"
			v-bind="formGroupProps">
			<div class="form-row">
				<div class="col-md-7 col-lg-5">
					<b-input-group prepend="@">
						<b-input v-model="venue.contacts.twitter" />
					</b-input-group>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Sito web"
			v-bind="formGroupProps"
			:state="!$v.urls.site.$error"
			invalid-feedback="Inserisci un URL valido, che inizi con 'http://' o 'https://'.">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input type="url" v-model="venue.urls.site" placeholder="http://" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Casinò online"
			v-bind="formGroupProps"
			:state="!$v.urls.online_casino.$error"
			invalid-feedback="Inserisci un URL valido, che inizi con 'http://' o 'https://'.">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input type="url" v-model="venue.urls.online_casino" placeholder="http://" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			label="Pagina Facebook"
			v-bind="formGroupProps"
			:state="!$v.urls.facebook.$error"
			invalid-feedback="Inserisci un URL valido, che inizi con 'http://' o 'https://'.">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input type="url" v-model="venue.urls.facebook" placeholder="http://" />
				</div>
			</div>
		</b-form-group>
	</div>
</template>