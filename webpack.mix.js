const { mix } = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for tje application, as well as bundling up all the JS files.
 |
 */

// App
mix.sass('resources/assets/sass/app/main.scss', 'public/css/app.css')
	.js('resources/assets/js/app/base.js', 'public/js/app/base.js')
	.js('resources/assets/js/app/home.js', 'public/js/app/home.js')
	.js('resources/assets/js/app/explore.js', 'public/js/app/explore.js')
	.js('resources/assets/js/app/detail.js', 'public/js/app/detail.js')
	.autoload({
		'jquery': ['$', 'jQuery'],
		'tether': ['Tether'],
		'vue': ['Vue'],
		'vue2-google-maps': ['VueGoogleMaps']
	})
	.extract([
		'jquery',
		'vue',
		'vue2-google-maps',
		'tether',
		'bootstrap'
	], 'public/js/app/vendor.js')
	.version();

// Admin
mix.sass('resources/assets/sass/admin/main.scss', 'public/css/admin.css')
	.js([
		'node_modules/jquery/dist/jquery.js',
		'node_modules/tether/dist/js/tether.js',
		'node_modules/bootstrap/dist/js/bootstrap.js',
		'resources/assets/js/admin/main.js',
	], 'public/js/admin.js')
	.version();