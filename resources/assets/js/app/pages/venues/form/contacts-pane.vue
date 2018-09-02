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
	<div class="my-5">
		<h4>{{ $t('pages.venue_form.contacts.title') }}</h4>
		<hr>
		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.contacts.phone')">
			<div class="form-row">
				<div class="col-md-7 col-lg-5">
					<b-input v-model="venue.contacts.phone" type="tel" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			:state="!$v.contacts.email.$error"
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.contacts.email')"
			:invalid-feedback="$t('pages.venue_form.contacts.email_error')">
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
			:label="$t('pages.venue_form.contacts.site')"
			:invalid-feedback="$t('pages.venue_form.contacts.url_error')">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input v-model="venue.urls.site" :placeholder="$t('pages.venue_form.contacts.url_placeholder')" type="url" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.urls.online_casino.$error"
			:label="$t('pages.venue_form.contacts.online_casino')"
			:invalid-feedback="$t('pages.venue_form.contacts.url_error')">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input v-model="venue.urls.online_casino" :placeholder="$t('pages.venue_form.contacts.url_placeholder')" type="url" />
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			:state="!$v.urls.facebook.$error"
			:label="$t('pages.venue_form.contacts.facebook')"
			:invalid-feedback="$t('pages.venue_form.contacts.url_error')">
			<div class="form-row">
				<div class="col-lg-9">
					<b-input v-model="venue.urls.facebook" :placeholder="$t('pages.venue_form.contacts.url_placeholder')" type="url" />
				</div>
			</div>
		</b-form-group>
	</div>
</template>