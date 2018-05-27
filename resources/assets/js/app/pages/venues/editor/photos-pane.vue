<script>
import BButton from 'bootstrap-vue/es/components/button/button';
import BProgress from 'bootstrap-vue/es/components/progress/progress';

import PgImageFrame from 'prontogioco/app/components/image-frame';
import PgConfirmModal from 'prontogioco/app/components/confirm-modal';
import PgUploader from 'vue-upload-component';

export default {
	name: 'PgVenueEditorPhotosPane',

	components: {
		BProgress,
		BButton,
		PgImageFrame,
		PgConfirmModal,
		PgUploader
	},
	
	props: {
		venue: {
			type: Object,
			required: true
		}
	},

	data() {
		return {
			uploaderHeaders: {
				'X-CSRF-TOKEN': pg.csrfToken
			},
			uploaderFiles: [],
			confirmDeleteOpen: false,
			currentPhoto: null
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
					this.venue.photos.push(newFile.response);
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
			this.venue.photos.splice(this.venue.photos.indexOf(this.currentPhoto), 1);
		}
	}
}
</script>

<template>
	<div>
		<div class="row" :class="{ 'bg-light': $refs.uploader && $refs.uploader.dropActive }">
			<!-- Current photos -->
			<div v-for="photo in venue.photos" class="col-4 col-md-3 mb-3">
				<input type="hidden" name="photos[]" :value="photo.id">
				<a :href="photo.resized_url" target="_blank">
					<pg-image-frame :src="photo.thumbnail_url" ratio="1:1" />
				</a>
				<b-button size="sm" variant="danger" block class="mt-2" @click="deletePhoto(photo)">Elimina</b-button>
			</div>

			<!-- Current uploads -->
			<div v-for="file in uploaderFiles" class="col-4 col-md-3 mb-3">
				<div class="embed-responsive embed-responsive-1by1 rounded border">
					<div class="embed-responsive-item d-flex flex-column align-items-center justify-content-center text-center">
						<span v-if="file.error" class="text-danger small"><strong>Errore</strong><br>{{ file.error }}</span>
						<template v-else>
							Caricamento
							<b-progress class="w-75 my-2" style="height: 2px" :value="file.progress" />
							{{ file.progress }}%
						</template>
					</div>
				</div>
				<b-button v-if="file.error" size="sm" variant="danger" block class="mt-2" @click="$refs.uploader.remove(file)">Rimuovi</b-button>
			</div>

			<!-- Uploader -->
			<div class="col-4 col-md-3 mb-3">
				<pg-uploader
					class="embed-responsive embed-responsive-1by1 rounded bg-active"
					ref="uploader"
					accept="image/*"
					multiple
					:drop="true"
					post-action="/files"
					:headers="uploaderHeaders"
					v-model="uploaderFiles"
					@input-file="onUploaderFileInput">
					<a class="embed-responsive-item text-primary border d-flex flex-column align-items-center justify-content-center">
						<div class="fa fa-plus"></div>
						Carica foto
					</a>
				</pg-uploader>
			</div>
		</div>

		<pg-confirm-modal v-model="confirmDeleteOpen"
			variant="danger"
			title="Rimuovi foto"
			ok-title="Rimuovi"
			@ok="confirmDeletePhoto">
			<p class="lead">Stai per <strong class="text-danger">rimuovere questa foto</strong>. Essa verrà effettivamente eliminata dalla galleria una volta salvati i dati dell'attività.</p>
			<img v-if="currentPhoto" :src="currentPhoto.thumbnail_url" class="img-fluid rounded">
		</pg-confirm-modal>
	</div>
</template>