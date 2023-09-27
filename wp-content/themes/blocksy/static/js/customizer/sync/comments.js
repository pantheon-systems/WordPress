import {
	watchOptionsWithPrefix,
	getOptionFor,
	getPrefixFor,
	applyPrefixFor,
} from './helpers'
import { handleBackgroundOptionFor } from './variables/background'

const getPrefix = () => {
	if (document.body.classList.contains('single')) {
		return 'post'
	}

	if (
		document.body.classList.contains('page') ||
		document.body.classList.contains('blog') ||
		document.body.classList.contains('post-type-archive-product')
	) {
		return 'page'
	}

	return false
}

export const renderComments = ({ prefix }) => {
	let container = document.querySelector('.ct-comments-container > div')

	if (!container) {
		return
	}

	container.classList.remove('ct-container', 'ct-container-narrow')
	container.classList.add(
		getOptionFor('comments_structure', prefix) === 'narrow'
			? 'ct-container-narrow'
			: 'ct-container'
	)

	if (window.DISQUS) {
		window.DISQUS.host._loadEmbed()
	}
}

watchOptionsWithPrefix({
	getPrefix,

	getOptionsForPrefix: ({ prefix }) => [`${prefix}_comments_structure`],

	render: renderComments,
})

export const getCommentsVariables = () => {
	const prefix = getPrefixFor()
	return {
		[`${prefix}_comments_narrow_width`]: {
			variable: 'narrow-container-max-width',
			selector: applyPrefixFor('.ct-comments-container', prefix),
			unit: 'px',
		},

		[`${prefix}_comments_font_color`]: [
			{
				selector: applyPrefixFor('.ct-comments', prefix),
				variable: 'color',
				type: 'color:default',
			},

			{
				selector: applyPrefixFor('.ct-comments', prefix),
				variable: 'linkHoverColor',
				type: 'color:hover',
			},
		],

		...handleBackgroundOptionFor({
			id: `${prefix}_comments_background`,
			selector: applyPrefixFor('.ct-comments-container', prefix),
		}),
	}
}
