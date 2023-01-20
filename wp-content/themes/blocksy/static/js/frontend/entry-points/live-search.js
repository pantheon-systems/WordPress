export const liveSearchEntryPoints = [
	{
		els: () => [
			[
				...document.querySelectorAll('.search-form[data-live-results]'),
			].filter(
				(el) =>
					!el.matches(
						'[id="search-modal"] .search-form[data-live-results]'
					) &&
					!el.matches(
						'.ct-sidebar .ct-widget .woocommerce-product-search'
					)
			),
		],
		load: () => import('../search-implementation'),
		mount: ({ mount, el }) => mount(el, {}),
		trigger: ['input'],
	},

	{
		els:
			'.ct-sidebar .ct-widget .search-form:not(.woocommerce-product-search)[data-live-results]',
		load: () => import('../search-implementation'),
		trigger: ['input'],
	},

	{
		els: '.ct-sidebar .ct-widget .woocommerce-product-search',
		load: () => import('../search-implementation'),
		mount: ({ mount, el }) => mount(el, {}),
		trigger: ['input'],
	},

	{
		els: '[id="search-modal"] .search-form[data-live-results]',
		load: () => import('../search-implementation'),
		mount: ({ mount, el }) =>
			mount(el, {
				mode: 'modal',
				perPage: 6,
			}),
		trigger: ['input'],
	},
]
