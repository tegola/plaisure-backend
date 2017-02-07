'use strict';

// const path = require('path');

module.exports = {
	module: {
		loaders: [{
			test: /\.vue$/,
			loader: 'vue-loader'
		}]
	},
	resolve: {
		alias: {
			'vue$': 'vue/dist/vue.common.js'
		}
	}
};