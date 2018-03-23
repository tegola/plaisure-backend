const mix = require('laravel-mix');
const path = require('path');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

const webpackConfig = {
	resolve: {
		alias: {
			prontogioco: path.resolve(__dirname, 'resources/assets/js/')
		}
	},
	plugins: []
};

if (!mix.inProduction()) {
	webpackConfig.plugins.push(
		new BundleAnalyzerPlugin({
			analyzerPort: 8889
		})
	);
}

mix.setPublicPath('public')
	.autoload({
		jquery: ['$', 'jQuery'], // Bootstrap
		'popper.js': 'Popper', // Bootstrap
	})
	.babelConfig({
		plugins: ['syntax-dynamic-import']
	})
	.webpackConfig(webpackConfig);

// Admin
mix.sass('resources/assets/sass/admin/main.scss', 'css/admin.css')
	.js('resources/assets/js/admin/main.js', 'js/admin.js')
	.version();

// App
mix.sass('resources/assets/sass/app/main.scss', 'css/app.css')
	.js('resources/assets/js/app/main.js', 'js/app.js')
	.version();