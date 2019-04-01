const path              = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');


const webPackModule = (production = true) => {
	return {
		rules: [
			{
				loader: 'babel-loader',
				test: /\.js$/,
				exclude: /node_modules/,
				query: {
					presets: ['es2015'],
				},
			},
			{
				test: /\.s?css$/,
				use: ExtractTextPlugin.extract({
					fallback: 'style-loader',
					use: [
						{
							loader: 'css-loader',
							options: {
								sourceMap: !production,
								minimize: production,
							},
						},
						{
							loader: 'sass-loader',
							options: {
								sourceMap: !production,
							},
						},
						{
							loader: 'postcss-loader',
						},
					],
				}),
			},
		],
	}
};

const componentSettings = (env) => {
	const isProduction = env === 'production';

	return {
		entry: ['whatwg-fetch', './src/js/component-settings-reports/app.js'],
		output: {
			path: path.join(__dirname,  'dist'),
			filename: path.join('js', 'component-settings-reports', 'app.js'),
		},
		plugins: [
			new ExtractTextPlugin(path.join('css', 'component-settings-reports', 'styles.css')),
		],
		module: webPackModule(!isProduction),
		devtool: isProduction ? '' : 'inline-source-map'
	};
};

const installerSupport = (env) => {
	const isProduction = env === 'production';
	return {
		entry: ['whatwg-fetch', './src/js/otgs-installer-support/app.js'],
		output: {
			path: path.join(__dirname,  'dist'),
			filename: path.join('js', 'otgs-installer-support', 'app.js')
		},
		plugins: [
			new ExtractTextPlugin(path.join('css', 'otgs-installer-support', 'styles.css'))
		],

		module: webPackModule(!isProduction),
		devtool: isProduction ? '' : 'inline-source-map',
	};
};

module.exports = [
	componentSettings,
	installerSupport
];

