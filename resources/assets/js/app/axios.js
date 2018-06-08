import Vue from 'vue'
import axios from 'axios'

const instance = axios.create({
	headers: {
		'X-Requested-With': 'XMLHttpRequest'
	}
})

// Register on all Vue instances
Vue.prototype.$axios = instance;

// Export as named export for use in .js files
export default instance;