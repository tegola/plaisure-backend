const mix = require('laravel-mix');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

mix.setPublicPath('public')
	.autoload({
		jquery: ['$', 'jQuery'], // Bootstrap
		'popper.js': 'Popper', // Bootstrap
	});


if (!mix.inProduction()) {
	mix.webpackConfig({
		plugins: [
			new BundleAnalyzerPlugin({
				analyzerPort: 8889
			})
		]
	});
}

// Admin
mix.sass('resources/assets/sass/admin/main.scss', 'css/admin.css')
	.js('resources/assets/js/admin/main.js', 'js/admin.js')
	.version();

// App
mix.sass('resources/assets/sass/app/main.scss', 'css/app.css')
	.js('resources/assets/js/app/main.js', 'js/app.js')
	.version();