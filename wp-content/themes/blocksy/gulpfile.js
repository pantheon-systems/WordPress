const gulp = require('gulp')
const buildProcess = require('ct-build-process')
const removeCode = require('gulp-remove-code')
const shell = require('gulp-shell')
const BundleAnalyzerPlugin =
	require('webpack-bundle-analyzer').BundleAnalyzerPlugin

const data = require('./package.json')

const wpExternals = {
	'@wordpress/element': 'window.wp.element',
	'@wordpress/media-utils': 'window.wp.mediaUtils',
	'@wordpress/keyboard-shortcuts': 'window.wp.keyboardShortcuts',
	'@wordpress/core-data': 'window.wp.coreData',
	'@wordpress/block-editor': 'window.wp.blockEditor',
	'@wordpress/blocks': 'window.wp.blocks',
	'@wordpress/primitives': 'window.wp.primitives',
	'@wordpress/hooks': 'window.wp.hooks',
	'@wordpress/components': 'window.wp.components',
	'@wordpress/date': 'window.wp.date',
	'@wordpress/edit-post': 'window.wp.editPost',
	'@wordpress/plugins': 'window.wp.plugins',
	'@wordpress/data': 'window.wp.data',
	'@wordpress/compose': 'window.wp.compose',
	'@wordpress/keycodes': 'window.wp.keycodes',
	'@wordpress/api-fetch': 'window.wp.apiFetch',
	'@wordpress/widgets': 'window.wp.widgets',
	'@wordpress/block-library': 'window.wp.blockLibrary',
	'blocksy-options': 'window.blocksyOptions',
	react: 'React',
	'react-dom': 'ReactDOM',
}

var options = {
	packageType: 'wordpress_theme',
	packageSlug: 'blocksy',
	packageI18nSlug: 'blocksy',

	browserSyncInitOptions: {
		logSnippet: false,
		port: 9669,
		domain: 'localhost',
		ui: {
			port: 9068,
		},
	},

	entries: [
		{
			entry: './static/js/main.js',
			output: {
				library: 'ctFrontend',
				libraryTarget: 'global',

				chunkLoadingGlobal: 'blocksyJsonP',
				path: './static/bundle/',
				chunkFilename: '[id].[chunkhash].js',
				publicPath: '',
			},

			/*
			optimization: {
				splitChunks: {
					cacheGroups: {
						default: false,
						vendors: false,

						popper: {
							chunks: 'all',
							test: /popper/,
						},
					},
				},
			},
            */
		},

		{
			entry: './static/js/events.js',
			output: {
				filename: 'events.js',
				path: './static/bundle/',
				chunkFilename: '[id].[chunkhash].js',

				library: 'ctEvents',
			},
		},

		{
			entry: './static/js/options.js',
			output: {
				filename: 'options.js',
				path: './static/bundle/',
				chunkFilename: '[id].[chunkhash].js',
				chunkLoadingGlobal: 'blocksyJsonP',
				library: 'blocksyOptions',
			},

			externals: {
				_: 'window._',
				jquery: 'jQuery',
				'ct-i18n': 'window.wp.i18n',
				'ct-events': 'ctEvents',
				underscore: 'window._',
				...wpExternals,
			},
		},

		{
			entry: './static/js/customizer/sync.js',
			output: {
				filename: 'sync.min.js',
				path: './static/bundle/',
				chunkLoadingGlobal: 'blocksyJsonP',
				library: 'blocksyCustomizerSync',
			},
			externals: {
				_: 'window._',
				jquery: 'jQuery',
				'ct-i18n': 'window.wp.i18n',
				'ct-events': 'window.ctEvents',
				underscore: 'window._',
				...wpExternals,
			},
		},

		{
			entry: './static/js/editor.js',
			output: {
				filename: 'editor.js',
				path: './static/bundle/',
				chunkLoadingGlobal: 'blocksyEditorJsonP',
			},
			externals: {
				_: 'window._',
				jquery: 'jQuery',
				'ct-i18n': 'window.wp.i18n',
				'ct-events': 'ctEvents',
				underscore: 'window._',
				...wpExternals,
			},
		},

		{
			entry: './static/js/customizer/controls.js',
			output: {
				filename: 'customizer-controls.js',
				path: './static/bundle/',
				chunkLoadingGlobal: 'blocksyJsonP',
				chunkFilename: '[id].[chunkhash].js',
				library: 'blocksyOptions',
			},
			externals: {
				_: 'window._',
				jquery: 'jQuery',
				'ct-i18n': 'window.wp.i18n',
				'ct-events': 'ctEvents',
				underscore: 'window._',
				...wpExternals,
			},
		},

		{
			entry: './admin/dashboard/static/js/main.js',
			output: {
				path: './admin/dashboard/static/bundle',
				chunkLoadingGlobal: 'blocksyJsonP',
			},
			externals: {
				jquery: 'jQuery',
				'ct-i18n': 'window.wp.i18n',
				'ct-events': 'ctEvents',
				underscore: 'window._',
				...wpExternals,
			},
		},
	],

	sassFiles: [
		{
			input: 'static/sass/frontend/main.scss',
			output: 'static/bundle',
			filename: 'main.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/5-modules/page-title/main.scss',
			output: 'static/bundle',
			filename: 'page-title.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/4-components/back-to-top.scss',
			output: 'static/bundle',
			filename: 'back-to-top.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/5-modules/widgets/non-critical-search-styles.scss',
			output: 'static/bundle',
			filename: 'non-critical-search-styles.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/admin-frontend.scss',
			output: 'static/bundle',
			filename: 'admin-frontend.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/3-actions/no-scripts.scss',
			output: 'static/bundle',
			filename: 'no-scripts.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/forminator/main.scss',
			output: 'static/bundle',
			filename: 'forminator.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/jet-woo-builder.scss',
			output: 'static/bundle',
			filename: 'jet-woo-builder.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/tribe-events.scss',
			output: 'static/bundle',
			filename: 'tribe-events.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/getwid.scss',
			output: 'static/bundle',
			filename: 'getwid.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/brizy.scss',
			output: 'static/bundle',
			filename: 'brizy.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/beaver.scss',
			output: 'static/bundle',
			filename: 'beaver.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/divi.scss',
			output: 'static/bundle',
			filename: 'divi.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/vc.scss',
			output: 'static/bundle',
			filename: 'vc.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/cf-7.scss',
			output: 'static/bundle',
			filename: 'cf-7.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/stackable.scss',
			output: 'static/bundle',
			filename: 'stackable.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/qubely.scss',
			output: 'static/bundle',
			filename: 'qubely.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/bbpress.scss',
			output: 'static/bundle',
			filename: 'bbpress.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/buddypress.scss',
			output: 'static/bundle',
			filename: 'buddypress.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/wpforms.scss',
			output: 'static/bundle',
			filename: 'wpforms.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/dokan.scss',
			output: 'static/bundle',
			filename: 'dokan.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/non-critical-styles.scss',
			output: 'static/bundle',
			filename: 'non-critical-styles.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/elementor.scss',
			output: 'static/bundle',
			filename: 'elementor-frontend.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/tutor/main.scss',
			output: 'static/bundle',
			filename: 'tutor.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/woocommerce/main.scss',
			output: 'static/bundle',
			filename: 'woocommerce.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/8-integrations/page-scroll-to-id.scss',
			output: 'static/bundle',
			filename: 'page-scroll-to-id.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/6-layout/sidebar/main.scss',
			output: 'static/bundle',
			filename: 'sidebar.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/5-modules/share-box/main.scss',
			output: 'static/bundle',
			filename: 'share-box.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/4-components/flexy.scss',
			output: 'static/bundle',
			filename: 'flexy.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/5-modules/comments.scss',
			output: 'static/bundle',
			filename: 'comments.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/4-components/author-box.scss',
			output: 'static/bundle',
			filename: 'author-box.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/frontend/4-components/posts-nav.scss',
			output: 'static/bundle',
			filename: 'posts-nav.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/backend/editor/main.scss',
			output: 'static/bundle',
			filename: 'editor.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/backend/editor/blocks/main.scss',
			output: 'static/bundle',
			filename: 'editor-styles.min',
		},

		{
			input: 'static/sass/backend/customizer/main.scss',
			output: 'static/bundle',
			filename: 'customizer-controls.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/backend/admin/elementor.scss',
			output: 'static/bundle',
			filename: 'elementor.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/backend/admin.scss',
			output: 'static/bundle',
			filename: 'options.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'admin/dashboard/static/sass/main.scss',
			output: 'admin/dashboard/static/bundle',
			filename: 'main.min',
			// header: buildProcess.headerFor(false, data),
		},

		// rtl
		{
			input: 'static/sass/frontend/main-rtl.scss',
			output: 'static/bundle',
			filename: 'main-rtl.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/backend/editor/main-rtl.scss',
			output: 'static/bundle',
			filename: 'editor-rtl.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/backend/customizer/main-rtl.scss',
			output: 'static/bundle',
			filename: 'customizer-controls-rtl.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'static/sass/backend/admin-rtl.scss',
			output: 'static/bundle',
			filename: 'options-rtl.min',
			// header: buildProcess.headerFor(false, data),
		},

		{
			input: 'admin/dashboard/static/sass/main-rtl.scss',
			output: 'admin/dashboard/static/bundle',
			filename: 'main-rtl.min',
			// header: buildProcess.headerFor(false, data),
		},
	],

	browserSyncEnabled: true,

	sassWatch: [
		'static/sass/**/*.scss',
		'admin/dashboard/static/sass/**/*.scss',
	],

	webpackDevtool: 'source-map',
	webpackExternals: {
		jquery: 'jQuery',
		'ct-i18n': 'window.wp.i18n',
		'ct-events': 'ctEvents',
		underscore: 'window._',
		'@wordpress/element': 'window.wp.element',
		'@wordpress/primitives': 'window.wp.primitives',
		'@wordpress/hooks': 'window.wp.hooks',
		'@wordpress/date': 'window.wp.date',
	},

	commonWebpackFields: {},

	webpackPlugins: [
		/*
		new BundleAnalyzerPlugin({
			analyzerPort: 0
		})
        */
	],

	webpackResolveAliases: {
		'ct-log': 'ct-wp-js-log',
	},

	babelAdditionalPlugins: [
		'babel-plugin-lodash',
		'@babel/plugin-transform-parameters',
	],

	modulesToCompileWithBabel: [
		'@wordpress/element',
		'flexy',
		'@wordpress/components',
	],

	filesToDeleteFromBuild: [
		'./build_tmp/build/Blocksy.code-workspace',
		'./build_tmp/build/tags',
		'./build_tmp/build/node_modules/',
		'./build_tmp/build/phpcs.xml.dist',
		'./build_tmp/build/child-theme/',
		'./build_tmp/build/composer.json',
		'./build_tmp/build/yarn.lock',
		'./build_tmp/build/wp-cli.yml',
		'./build_tmp/build/docs',
		'./build_tmp/build/extensions.json',
		// './build_tmp/build/gulpfile.js',
		// './build_tmp/build/package.json',
		'./build_tmp/build/psds',
		'./build_tmp/build/ruleset.xml',
		'./build_tmp/build/tests',
		'./build_tmp/build/scripts',
		'./build_tmp/build/inc/browser-sync.php',
		// './build_tmp/build/admin/dashboard/static/{js,sass}',
		// './build_tmp/build/static/{js,sass}'
		],

	toClean: ['static/bundle/', 'admin/dashboard/static/bundle/'],

	babelJsxPlugin: 'react',
	babelJsxReactPragma: 'createElement',
}

buildProcess.registerTasks(gulp, options)

gulp.task(
	'gettext-generate-js',
	shell.task(
		[
			'cross-env NODE_ENV_GETTEXT=true NODE_ENV=production yarn gulp build --silent',
		],
		{
			ignoreErrors: true,
			verbose: true,
		}
	)
)

gulp.task(
	'gettext-generate',
	gulp.series(
		'gettext-generate-js',
		'gettext-generate:php',
		shell.task(
			[
				"msgcat languages/blocksy-php.pot languages/ct-js.pot | grep -v '#-#-#-#' > ./languages/blocksy.pot && rm ./languages/blocksy-php.pot ./languages/ct-js.pot",
			],
			{
				ignoreErrors: true,
				verbose: true,
			}
		)

		/*
		shell.task(['yarn build'], {
			ignoreErrors: true,
			verbose: true,
		})
        */
	)
)
