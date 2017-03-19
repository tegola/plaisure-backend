const { mix } = require('laravel-mix');

// Admin
mix.sass('resources/assets/sass/admin/main.scss', 'public/css/admin.css');
mix.js('resources/assets/js/admin/maintain.js', 'public/js/admin')
	// .autoload({
	// 	'lodash': ['_', 'lodash'],
	// 	'jquery': ['$', 'jQuery'],
	// 	'tether': ['Tether'],
	// 	'vue': ['Vue'],
	// 	'vue2-google-maps': ['VueGoogleMaps']
	// })
	// .extract(['lodash', 'jquery', 'vue', 'vue2-google-maps', 'tether', 'bootstrap'], 'public/js/admin/vendor.js')
	.version();

// App
mix.sass('resources/assets/sass/app/main.scss', 'public/css/app.css');
mix.js('resources/assets/js/app/base.js', 'public/js/app')
	.js('resources/assets/js/app/home.js', 'public/js/app')
	.js('resources/assets/js/app/explore.js', 'public/js/app')
	.js('resources/assets/js/app/detail.js', 'public/js/app')
	.autoload({
		'lodash': ['_', 'lodash'],
		'jquery': ['$', 'jQuery'],
		'tether': ['Tether'],
		'vue': ['Vue'],
		'vue2-google-maps': ['VueGoogleMaps']
	})
	.extract(['lodash', 'jquery', 'vue', 'vue2-google-maps', 'tether', 'bootstrap'], 'public/js/app/vendor.js')
	.version();