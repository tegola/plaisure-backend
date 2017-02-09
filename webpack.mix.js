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

// App stylesheets
mix.sass('resources/assets/sass/app/main.scss', 'public/css/app.css');

// App scripts
mix.js([
	//'node_modules/jquery/dist/jquery.js',
	//'node_modules/tether/dist/js/tether.js',
	//'node_modules/bootstrap/dist/js/bootstrap.js',
	//'node_modules/bootstrap-3-typeahead/bootstrap3-typeahead.js',
	'resources/assets/js/app/main.js',
	'resources/assets/js/app/search-form.js'
], 'public/js/app/main.js')
.version();

mix.js('resources/assets/js/app/explore.js', 'public/js/app/explore.js')
.version();

// Admin stylesheets
mix.sass('resources/assets/sass/admin/main.scss', 'public/css/admin.css')
.version();

// Admin scripts
mix.js([
	'node_modules/jquery/dist/jquery.js',
	'node_modules/tether/dist/js/tether.js',
	'node_modules/bootstrap/dist/js/bootstrap.js',
	'resources/assets/js/admin/main.js',
], 'public/js/admin.js')
.version();