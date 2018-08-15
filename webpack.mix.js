const mix = require('laravel-mix');
const path = require('path');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

const webpackConfig = {
	resolve: {
		alias: {
			assets: path.resolve(__dirname, 'resources/assets/'),
			prontogioco: path.resolve(__dirname, 'resources/assets/js/')
		}
	},
	plugins: [],
	output: {
		chunkFilename: 'js/chunks/[name].js',
		publicPath: '/'
	}
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
		'popper.js': 'Popper' // Bootstrap
	})
	.babelConfig({
		plugins: [
			// 'transform-object-rest-spread',
			'syntax-dynamic-import'
		]
	})
	.webpackConfig(webpackConfig);

// Admin
mix.sass('resources/assets/sass/admin/main.scss', 'css/admin.css')
	.js('resources/assets/js/admin/index.js', 'js/admin.js')
	.version();

// App
mix.sass('resources/assets/sass/app/main.scss', 'css/app.css')
	.js('resources/assets/js/app/index.js', 'js/app.js')
	.version();