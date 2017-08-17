let mix = require('laravel-mix');

mix.autoload({
	jquery: ['jQuery'], // Bootstrap
	'popper.js/dist/umd/popper.js': ['Popper'] // Bootstrap
});

// Admin
mix.sass('resources/assets/sass/admin/main.scss', 'public/css/admin.css')
	.js('resources/assets/js/admin/main.js', 'public/js/admin.js')
	.version();

// App
mix.sass('resources/assets/sass/app/main.scss', 'public/css/app.css')
	.js('resources/assets/js/app/main.js', 'public/js/app.js')
	.version();

