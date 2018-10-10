<script>
import BButton from 'bootstrap-vue/es/components/button/button';
import BProgress from 'bootstrap-vue/es/components/progress/progress';
import VueUploader from 'vue-upload-component'; // FIXME: Make custom component

import PgImageFrame from 'prontogioco/app/components/image-frame';
import PgConfirmModal from 'prontogioco/app/components/confirm-modal';

export default {
	name: 'PgVenueFormPhotosPane',

	components: {
		BProgress,
		BButton,
		PgImageFrame,
		PgConfirmModal,
		VueUploader
	},

	props: {
		venueId: {
			type: [String, Number],
			default: null
		}
	},

	data() {
		return {
			uploaderFiles: [],
			photoItemClass: 'col-6 col-md-4 col-xl-3 mb-3',
			confirmDeleteOpen: false,
			currentPhoto: null
		};
	},

	computed: {
		storeName() {
			return `venueForm/${this.venueId || 'new'}`;
		},

		venue() {
			return this.$store.state[this.storeName].venue;
		},

		venuePhotos: {
			get() {
				return this.venue.photos;
			},
			set(value) {
				this.$store.commit(`${this.storeName}/setVenueField`, {
					field: 'photos',
					value
				});
			}
		},

		subscription() {
			return this.venue.subscription;
		},

		uploaderHeaders() {
			const accessToken = this.$store.state.user.accessToken;

			return {
				'Authorization': `Bearer ${accessToken}`,
				'X-Requested-With': 'XMLHttpRequest'
			};
		}
	},

	methods: {
		onUploaderFileInput(newFile, oldFile) {
			// Update
			if (newFile && oldFile) {
				// FIXME: Qui dovremmo controllare l'errore del server e
				// scriverlo nell'istanza del file, quindi mostrarlo
				if (newFile.response && newFile.response.file) {
					newFile.error = newFile.response.file[0];
					// newFile = this.$refs.uploader.update(newFile, { error: newFile.response.file });
				}

				// Upload successful
				if (newFile.success !== oldFile.success) {
					const photos = this.venuePhotos.slice();
					photos.push(newFile.response);
					this.venuePhotos = photos;

					this.$refs.uploader.remove(newFile);
				}
			}

			// Automatic upload
			if (Boolean(newFile) !== Boolean(oldFile) || oldFile.error !== newFile.error) {
				if (!this.$refs.uploader.active) {
					this.$refs.uploader.active = true;
				}
			}
		},

		deletePhoto(photo) {
			this.currentPhoto = photo;
			this.confirmDeleteOpen = true;
		},

		confirmDeletePhoto() {
			const photos = this.venuePhotos.slice();
			photos.splice(photos.indexOf(this.currentPhoto), 1);
			this.venuePhotos = photos;
		}
	}
};
</script>

<template>
	<div class="my-5">
		<h5>{{ $t('pages.venue_form.photos.title') }}</h5>
		<i18n tag="p" path="pages.venue_form.photos.intro">
			<span place="count">{{ subscription.photo_limit }}</span>
			<a href="#" place="action"><strong>{{ $t('pages.venue_form.photos.intro_action') }}</strong></a>
		</i18n>
		<hr>
		<div :class="{ 'bg-light': $refs.uploader && $refs.uploader.dropActive }" class="row">
			<!-- Current photos -->
			<div v-for="photo in venuePhotos" :key="photo.id" :class="photoItemClass">
				<a :href="photo.resized_url" target="_blank">
					<pg-image-frame :src="photo.thumbnail_url" ratio="1:1" class="rounded" />
				</a>
				<b-button size="sm" variant="danger" block class="mt-2" @click="deletePhoto(photo)">{{ $t('common.actions.delete') }}</b-button>
			</div>

			<!-- Current uploads -->
			<div v-for="(file, index) in uploaderFiles" :key="index" :class="photoItemClass">
				<div class="embed-responsive embed-responsive-1by1 rounded border">
					<div class="embed-responsive-item d-flex flex-column align-items-center justify-content-center text-center">
						<span v-if="file.error" class="text-danger small"><strong>{{ $t('common.status.error') }}</strong><br>{{ file.error }}</span>
						<template v-else>
							{{ $t('common.status.loading') }}
							<b-progress :value="parseFloat(file.progress)" class="w-75 my-2" style="height: 2px" />
							{{ file.progress }}%
						</template>
					</div>
				</div>
				<b-button v-if="file.error" size="sm" variant="danger" block class="mt-2" @click="$refs.uploader.remove(file)">{{ $t('common.actions.remove') }}</b-button>
			</div>

			<!-- Uploader -->
			<div v-if="subscription.photo_limit > (venuePhotos.length + uploaderFiles.length)" :class="photoItemClass">
				<vue-uploader
					ref="uploader"
					:drop="true"
					:headers="uploaderHeaders"
					v-model="uploaderFiles"
					class="embed-responsive embed-responsive-1by1 rounded"
					accept="image/*"
					multiple
					post-action="/api/files"
					@input-file="onUploaderFileInput">
					<a class="embed-responsive-item text-primary border d-flex flex-column align-items-center justify-content-center">
						<pg-icon icon="plus" />
						{{ $t('pages.venue_form.photos.upload') }}
					</a>
				</vue-uploader>
			</div>
		</div>

		<pg-confirm-modal v-model="confirmDeleteOpen"
			:title="$t('pages.venue_form.photos.remove.title')"
			:ok-title="$t('common.actions.remove')"
			variant="danger"
			@ok="confirmDeletePhoto">
			<i18n tag="p" class="lead" path="pages.venue_form.photos.remove.intro">
				<strong class="text-danger" place="action">{{ $t('pages.venue_form.photos.remove.intro_action') }}</strong>
			</i18n>
			<img v-if="currentPhoto" :src="currentPhoto.thumbnail_url" class="img-fluid rounded">
		</pg-confirm-modal>
	</div>
</template>