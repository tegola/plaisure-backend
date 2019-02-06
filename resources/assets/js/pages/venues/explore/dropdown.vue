<script>
import bDropdown from 'bootstrap-vue/es/components/dropdown/dropdown.js';

export default {
	name: 'PgExplorePageDropdown',

	mixins: [bDropdown],

	props: {
		autoHide: {
			type: Boolean,
			default: true
		}
	},

	methods: {
		rootCloseListener(vm) {
			// Prevent close when dropdown item is clicked
			if (vm instanceof MouseEvent && !this.autoHide) return;

			// close when other dropdown on page is opened
			bDropdown.mixins[1].methods.rootCloseListener.call(this, vm);
		}
	}
};
</script>