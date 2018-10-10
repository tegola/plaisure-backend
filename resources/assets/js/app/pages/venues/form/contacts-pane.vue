<script>
import autoHttp from 'prontogioco/app/directives/auto-http';
import _extend from 'lodash/extend';

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

	directives: {
		autoHttp
	},

	props: {
		venueId: {
			type: [String, Number],
			default: null
		}
	},

	data() {
		return {
			formGroupProps
		};
	},

	computed: {
		storeName() {
			return `venueForm/${this.venueId || 'new'}`;
		},

		venue() {
			return this.$store.state[this.storeName].venue;
		},

		$v() {
			return this.$parent.$v.venue;
		}
	},

	methods: {
		onContactInput (name, value) {
			const contacts = _extend({}, this.venue.contacts, {
				[name]: value
			});

			this.$store.commit(`${this.storeName}/setVenueField`, {
				field: 'contacts',
				value: contacts
			});
		},

		onUrlInput (name, value) {
			const urls = _extend({}, this.venue.urls, {
				[name]: value
			});

			this.$store.commit(`${this.storeName}/setVenueField`, {
				field: 'urls',
				value: urls
			});
		}
	}
};
</script>

<template>
	<div class="my-5">
		<h5>{{ $t('pages.venue_form.contacts.title') }}</h5>
		<hr>
		<b-form-group
			v-bind="formGroupProps"
			:label="$t('pages.venue_form.contacts.phone')">
			<div class="form-row">
				<div class="col-md-7 col-lg-5">
					<b-input
						:value="venue.contacts.phone"
						type="tel"
						@input="onContactInput('tel', $event)"
					/>
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
					<b-input
						:placeholder="$t('pages.venue_form.contacts.email_placeholder' )"
						:value="venue.contacts.email"
						type="email"
						@input="onContactInput('email', $event)"
					/>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			label="Facebook Messenger">
			<div class="form-row">
				<div class="col-md-7 col-lg-5">
					<b-input
						:value="venue.contacts.facebook"
						@input="onContactInput('facebook', $event)"
					/>
				</div>
			</div>
		</b-form-group>

		<b-form-group
			v-bind="formGroupProps"
			label="Twitter">
			<div class="form-row">
				<div class="col-md-7 col-lg-5">
					<b-input-group prepend="@">
						<b-input
							:value="venue.contacts.twitter"
							@input="onContactInput('twitter', $event)"
						/>
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
					<b-input
						v-auto-http
						:placeholder="$t('pages.venue_form.contacts.url_placeholder')"
						:value="venue.urls.site"
						type="url"
						@input="onUrlInput('site', $event)"
					/>
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
					<b-input
						v-auto-http
						:placeholder="$t('pages.venue_form.contacts.url_placeholder')"
						:value="venue.urls.online_casino"
						type="url"
						@input="onUrlInput('online_casino', $event)"
					/>
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
					<b-input
						v-auto-http
						:placeholder="$t('pages.venue_form.contacts.url_placeholder')"
						:value="venue.urls.facebook"
						type="url"
						@input="onUrlInput('facebook', $event)"
					/>
				</div>
			</div>
		</b-form-group>
	</div>
</template>