var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

// No source maps (.map files)
elixir.config.sourcemaps = false;

// Build
elixir(function(mix) {
	// App stylesheets
	mix.sass(['app/main.scss'], 'public/css/app.css');

	// App scripts
	mix.scripts([
		'../../../node_modules/jquery/dist/jquery.js',
		'../../../node_modules/tether/dist/js/tether.js',
		'../../../node_modules/bootstrap/dist/js/bootstrap.js',
		'../../../node_modules/bootstrap-3-typeahead/bootstrap3-typeahead.js',
		'app/main.js',
		'app/search-form.js'
	], 'public/js/app/main.js');

	mix.webpack('app/explore.js', 'public/js/app/explore.js');

	// Admin stylesheets
	mix.sass(['admin/main.scss'], 'public/css/admin.css');

	// Admin scripts
	mix.scripts([
		'../../../node_modules/jquery/dist/jquery.js',
		'../../../node_modules/tether/dist/js/tether.js',
		'../../../node_modules/bootstrap/dist/js/bootstrap.js',
		'admin/main.js',
	], 'public/js/admin.js');
});
