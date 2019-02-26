const path              = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

const webPackModule = (production = true ) => {
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



const uiDev = () => {
	return {
		entry: ['whatwg-fetch', './js/ui/app.js'],
		output: {
			path: path.join(__dirname, '..', 'dist'),
			filename: path.join('js', 'ui', 'app.dev.js'),
		},
		module: webPackModule(false),
		plugins: [
			new ExtractTextPlugin(path.join('css', 'ui', 'styles.dev.css')),
		],
		devtool: 'inline-source-map',
	};
};

const uiProd = () => {
	return {
		entry: ['whatwg-fetch', './js/ui/app.js'],
		output: {
			path: path.join(__dirname, '..', 'dist'),
			filename: path.join('js', 'ui', 'app.js'),
		},
		module: webPackModule(true),
		plugins: [
			new ExtractTextPlugin(path.join('css', 'ui', 'styles.css')),
		],
		devtool: '',
	};
};

module.exports = [
	uiDev,
	uiProd,
];