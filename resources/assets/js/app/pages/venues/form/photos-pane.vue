<script>
import BButton from 'bootstrap-vue/es/components/button/button';
import BProgress from 'bootstrap-vue/es/components/progress/progress';

import PgImageFrame from 'prontogioco/app/components/image-frame';
import PgConfirmModal from 'prontogioco/app/components/confirm-modal';
import PgUploader from 'vue-upload-component';

export default {
	name: 'PgVenueFormPhotosPane',

	components: {
		BProgress,
		BButton,
		PgImageFrame,
		PgConfirmModal,
		PgUploader
	},

	props: {
		photos: {
			type: Array,
			default: () => []
		}
	},

	data() {
		return {
			mutablePhotos: this.photos,
			uploaderHeaders: {
				// 'X-CSRF-TOKEN': pg.csrfToken
			},
			uploaderFiles: [],
			confirmDeleteOpen: false,
			currentPhoto: null
		};
	},

	watch: {
		photos() {
			this.mutablePhotos = this.photos;
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
					this.mutablePhotos.push(newFile.response);
					this.$emit('update:photos', this.mutablePhotos);
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
			this.mutablePhotos.splice(this.mutablePhotos.indexOf(this.currentPhoto), 1);
			this.$emit('update:photos', this.mutablePhotos);
		}
	}
};
</script>

<template>
	<div>
		<div :class="{ 'bg-light': $refs.uploader && $refs.uploader.dropActive }" class="row">
			<!-- Current photos -->
			<div v-for="photo in mutablePhotos" class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
				<a :href="photo.resized_url" target="_blank">
					<pg-image-frame :src="photo.thumbnail_url" ratio="1:1" class="rounded" />
				</a>
				<b-button size="sm" variant="danger" block class="mt-2" @click="deletePhoto(photo)">{{ $t('common.actions.delete') }}</b-button>
			</div>

			<!-- Current uploads -->
			<div v-for="file in uploaderFiles" class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
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
			<div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
				<pg-uploader
					ref="uploader"
					:drop="true"
					:headers="uploaderHeaders"
					v-model="uploaderFiles"
					class="embed-responsive embed-responsive-1by1 rounded"
					accept="image/*"
					multiple
					post-action="/files"
					@input-file="onUploaderFileInput">
					<a class="embed-responsive-item text-primary border d-flex flex-column align-items-center justify-content-center">
						<pg-icon icon="plus" />
						{{ $t('pages.venue_form.photos.upload') }}
					</a>
				</pg-uploader>
			</div>
		</div>

		<pg-confirm-modal v-model="confirmDeleteOpen"
			:title="$t('pages.venue_form.photos.remove.title')"
			:cancel-title="$t('common.actions.cancel')"
			:ok-title="$t('common.actions.remove')"
			variant="danger"
			@ok="confirmDeletePhoto">
			<i18n tag="p" class="lead" path="pages.venue_form.photos.remove.intro">
				<strong class="text-danger" place="action">{{ $t('pages.venue_form.photos.remove.intro_action') }}</strong>
			</i18n>
			<p class="lead">Stai per <strong class="text-danger">rimuovere questa foto</strong>. Essa verrà effettivamente eliminata dalla galleria una volta salvati i dati dell'attività.</p>
			<img v-if="currentPhoto" :src="currentPhoto.thumbnail_url" class="img-fluid rounded">
		</pg-confirm-modal>
	</div>
</template>