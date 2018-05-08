<script>
import BInput from 'bootstrap-vue/es/components/form-input/form-input';
import BInputGroup from 'bootstrap-vue/es/components/input-group/input-group';

export default {
	name: 'PgVenueEditorContactsPane',

	components: {
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
			mutableVenue: this.venue
		};
	},

	watch: {
		mutableVenue: {
			deep: true,
			handler() {
				this.$emit('update:venue', this.mutableVenue);
			}
		}
	}
}
</script>

<template>
	<div>
		<h4>Contatti</h4>
		<hr>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label>Telefono</label>
					<b-input type="tel" v-model="mutableVenue.contacts.phone" />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label>E-mail</label>
					<b-input type="email" v-model="mutableVenue.contacts.email" />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label>Facebook Messenger</label>
					<b-input v-model="mutableVenue.contacts.facebook" />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label>Twitter</label>
					<b-input-group prepend="@">
						<b-input v-model="mutableVenue.contacts.twitter" />
					</b-input-group>
				</div>
			</div>
		</div>

		<h5 class="mt-4">Indirizzi web</h5>
		<hr>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label>Sito web</label>
					<b-input type="url" v-model="mutableVenue.urls.site" />
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>Casinò online</label>
					<b-input type="url" v-model="mutableVenue.urls.online_casino" />
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>Pagina Facebook</label>
					<b-input type="url" v-model="mutableVenue.urls.facebook" />
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>Pagina TripAdvisor</label>
					<b-input type="url" v-model="mutableVenue.urls.tripadvisor" />
				</div>
			</div>
		</div>
	</div>
</template>