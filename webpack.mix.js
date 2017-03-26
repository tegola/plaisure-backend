let mix = require('laravel-mix');

// Autoload libraries
mix.autoload({
	'lodash': ['_', 'lodash'],
	'jquery': ['$', 'jQuery'],
	'tether': ['Tether'],
	'vue': ['Vue'],
	'vue2-google-maps': ['VueGoogleMaps']
});

// Extract some libraries in a common file
mix.extract([
	'lodash',
	'jquery',
	'vue',
	'vue2-google-maps',
	'tether',
	'bootstrap'
], 'public/js/vendor.js');

// Admin
mix.sass('resources/assets/sass/admin/main.scss', 'public/css/admin.css');
mix.js('resources/assets/js/admin/main.js', 'public/js/admin')
	.version();

// App
mix.sass('resources/assets/sass/app/main.scss', 'public/css/app.css');
mix.js('resources/assets/js/app/base.js', 'public/js/app')
	.js('resources/assets/js/app/home.js', 'public/js/app')
	.js('resources/assets/js/app/explore.js', 'public/js/app')
	.js('resources/assets/js/app/detail.js', 'public/js/app')
	.version();