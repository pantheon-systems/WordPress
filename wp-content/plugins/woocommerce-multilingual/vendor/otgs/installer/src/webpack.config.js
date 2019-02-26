const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

const webPackModule = {
	rules: [
		{
			loader:  'babel-loader',
			test:    /\.js$/,
			exclude: /node_modules/,
			query:   {
				presets: ['es2015'],
			},
		}, {
			test: /\.s?css$/,
			use:  ExtractTextPlugin.extract({
																				fallback: 'style-loader',
																				use:      [
																					{
																						loader:  'css-loader',
																						options: {
																							sourceMap: true,
																						},
																					}, {
																						loader:  'sass-loader',
																						options: {
																							sourceMap: true,
																						},
																					}, {
																						loader: 'postcss-loader',
																					},
																				],
																			}),
		},
	],
};

const ui = (env) => {
	const isProduction = env === 'production';

	return {
		entry:   ['whatwg-fetch', './js/ui/app.js'],
		output:  {
			path:     path.join(__dirname, '..', 'dist'),
			filename: path.join('js', 'ui', 'app.js'),
		},
		module:  webPackModule,
		plugins: [
			new ExtractTextPlugin(path.join('css', 'ui', 'styles.css')),
		],
		devtool: isProduction ? '' : 'inline-source-map',
	};
};

module.exports = [
	ui,
];