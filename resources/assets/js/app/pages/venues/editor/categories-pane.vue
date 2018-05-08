<script>
import BCheckbox from 'bootstrap-vue/es/components/form-checkbox/form-checkbox';

export default {
	name: 'PgVenueEditorCategoriesPane',

	components: {
		BCheckbox
	},

	props: {
		venue: {
			type: Object,
			required: true
		},
		categories: {
			type: Array,
			default: []
		}
	}
}
</script>

<template>
	<div>
		<h4>Categorie</h4>
		<hr>
		<div class="row form-group">
			<div class="col-sm-6 col-md-4 col-lg-3" v-for="category in categories" :key="category.id">
				<label>
					<b-checkbox :value="category.id" v-model="venue.category_ids">
						{{ category.name}}
					</b-checkbox>
				</label>
			</div>
		</div>
	</div>
</template>