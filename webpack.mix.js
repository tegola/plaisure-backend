const mix = require('laravel-mix');
const BabiliPlugin = require('babili-webpack-plugin'); // FIXME: Not needed anymore
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

const webpackConfig = {
	plugins: []
};

mix.autoload({
	jquery: ['$', 'jQuery'], // Bootstrap
	'popper.js': 'Popper', // Bootstrap
});

// Admin
mix.sass('resources/assets/sass/admin/main.scss', 'public/css/admin.css')
	.js('resources/assets/js/admin/main.js', 'public/js/admin.js')
	.version();

// App
mix.sass('resources/assets/sass/app/main.scss', 'public/css/app.css')
	.js('resources/assets/js/app/main.js', 'public/js/app.js')
	.version();


if (mix.inProduction()) {
	// Use Babili instead of UglifyJS, since it won't transpile ES6
	mix.options({
		uglify: false
	});
	webpackConfig.plugins.push(
		new BabiliPlugin()
	);
} else {
	webpackConfig.plugins.push(
		new BundleAnalyzerPlugin({
			analyzerPort: 8889
		})
	);
}

mix.webpackConfig(webpackConfig);