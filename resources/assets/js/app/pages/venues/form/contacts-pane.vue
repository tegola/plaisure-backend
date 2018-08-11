<script>
import BFormGroup from 'bootstrap-vue/es/components/form-group/form-group';
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BInputGroup from 'bootstrap-vue/es/components/input-group/input-group';

import formGroupProps from './form-group-props';

export default {
	name: 'PgVenueFormGeneralPane',

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
		}
	}
};
</script>

<template>
	<div>
		<b-form-group
			v-bind="formGroupProps"
			label="Telefono">
			<div class="form-row">
				<div class="col-md-7 col-lg-5">
					<b-input v-model="venue.contacts.phone" type="tel" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			:state="!$v.contacts.email.$error"
			v-bind="formGroupProps"
			label="E-mail"
			invalid-feedback="Inserisci un indirizzo email valido.">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input v-model="venue.contacts.email" type="email" placeholder="Es.: nome@gmail.com"/>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			label="Facebook Messenger">
			<div class="form-row">
				<div class="col-md-7 col-lg-5">
					<b-input v-model="venue.contacts.facebook" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			label="Twitter">
			<div class="form-row">
				<div class="col-md-7 col-lg-5">
					<b-input-group prepend="@">
						<b-input v-model="venue.contacts.twitter" />
					</b-input-group>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.urls.site.$error"
			label="Sito web"
			invalid-feedback="Inserisci un URL valido, che inizi con 'http://' o 'https://'.">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input v-model="venue.urls.site" type="url" placeholder="http://" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.urls.online_casino.$error"
			label="Casinò online"
			invalid-feedback="Inserisci un URL valido, che inizi con 'http://' o 'https://'.">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input v-model="venue.urls.online_casino" type="url" placeholder="http://" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.urls.facebook.$error"
			label="Pagina Facebook"
			invalid-feedback="Inserisci un URL valido, che inizi con 'http://' o 'https://'.">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input v-model="venue.urls.facebook" type="url" placeholder="http://" />
				</div>
			</div>
		</b-form-group>
	</div>
</template>