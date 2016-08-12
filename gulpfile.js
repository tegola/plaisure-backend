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

elixir(function(mix) {
    mix.sass('app.scss');

    mix.scripts([
    	'../../../node_modules/jquery/dist/jquery.js',
    	'../../../node_modules/tether/dist/js/tether.js',
    	'../../../node_modules/bootstrap/dist/js/bootstrap.js',
    	'../../../node_modules/bootstrap-3-typeahead/bootstrap3-typeahead.js',

        'app.js',
    	'search-form.js'
    ], 'public/js/app.js');
});
