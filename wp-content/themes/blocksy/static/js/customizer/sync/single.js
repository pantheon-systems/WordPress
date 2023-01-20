import {
	setRatioFor,
	watchOptionsWithPrefix,
	responsiveClassesFor,
	getOptionFor,
	getPrefixFor,
} from './helpers'

import { renderSingleEntryMeta } from './helpers/entry-meta'

watchOptionsWithPrefix({
	getOptionsForPrefix: ({ prefix }) => [
		`${prefix}_share_box_title`,
		`${prefix}_share_box_visibility`,

		`${prefix}_author_box_visibility`,
		`${prefix}_post_nav_title_visibility`,
		`${prefix}_post_nav_thumb_visibility`,
		`${prefix}_post_nav_visibility`,
		`${prefix}_comments_structure`,

		// `${prefix}_related_posts_columns`,
		`${prefix}_related_featured_image_ratio`,
		`${prefix}_related_label`,
		`${prefix}_related_visibility`,
		`${prefix}_related_structure`,
		`${prefix}_related_posts_containment`,
		`${prefix}_related_single_meta_elements`,
	],
	render: ({ prefix, id }) => {
		const visibilities = [
			{ selector: '.ct-share-box', id: 'share_box_visibility' },
			{ selector: '.author-box', id: 'author_box_visibility' },
			{
				selector: '.post-navigation .item-title',
				id: 'post_nav_title_visibility',
			},
			{
				selector: '.post-navigation .ct-image-container',
				id: 'post_nav_thumb_visibility',
			},
			{ selector: '.post-navigation', id: 'post_nav_visibility' },
		]

		if (id === `${prefix}_share_box_title`) {
			Array.from(
				document.querySelectorAll('.ct-share-box .ct-module-title')
			).map((el) => {
				el.innerHTML = getOptionFor('share_box_title', prefix)
			})
		}

		visibilities.map((visibilityDescriptor) => {
			if (id !== `${prefix}_${visibilityDescriptor.id}`) {
				return
			}

			Array.from(
				document.querySelectorAll(visibilityDescriptor.selector)
			).map((el) => {
				responsiveClassesFor(
					getOptionFor(visibilityDescriptor.id, prefix),
					el
				)
			})
		})

		if (id === `${prefix}_comments_structure`) {
			Array.from(document.querySelectorAll('.ct-comments-container')).map(
				(el) => {
					let container = getOptionFor('comments_structure', prefix)

					el.firstElementChild.classList.remove(
						'ct-container',
						'ct-container-narrow'
					)

					el.firstElementChild.classList.add(
						container === 'narrow'
							? 'ct-container-narrow'
							: 'ct-container'
					)
				}
			)
		}

		/*
		if (id === `${prefix}_related_posts_columns`) {
			Array.from(document.querySelectorAll('.ct-related-posts')).map(
				(el) => {
					el.dataset.layout = `grid:columns-${getOptionFor(
						'related_posts_columns',
						prefix
					)}`
				}
			)
		}
        */

		if (id === `${prefix}_related_featured_image_ratio`) {
			Array.from(
				document.querySelectorAll(
					'.ct-related-posts .ct-image-container'
				)
			).map((el) => {
				setRatioFor(
					getOptionFor('related_featured_image_ratio', prefix),
					el
				)
			})
		}

		if (id === `${prefix}_related_label`) {
			Array.from(
				document.querySelectorAll('.ct-related-posts .ct-block-title')
			).map((el) => {
				el.innerHTML = getOptionFor('related_label', prefix)
			})
		}

		if (
			id === `${prefix}_related_posts_containment` ||
			id === `${prefix}_related_visibility`
		) {
			Array.from(
				document.querySelectorAll('.ct-related-posts-container')
			).map((el) => {
				responsiveClassesFor(
					getOptionFor('related_visibility', prefix),
					el
				)
			})

			if (
				getOptionFor('related_posts_containment', prefix) !==
				'separated'
			) {
				Array.from(document.querySelectorAll('.ct-related-posts')).map(
					(el) => {
						responsiveClassesFor(
							getOptionFor('related_visibility', prefix),
							el
						)
					}
				)
			}
		}

		if (id === `${prefix}_related_structure`) {
			Array.from(
				document.querySelectorAll('.ct-related-posts-container')
			).map((el) => {
				let container = getOptionFor('related_structure', prefix)

				el.firstElementChild.classList.remove(
					'ct-container',
					'ct-container-narrow'
				)

				el.firstElementChild.classList.add(
					container === 'narrow'
						? 'ct-container-narrow'
						: 'ct-container'
				)
			})
		}

		if (id === `${prefix}_related_single_meta_elements`) {
			Array.from(
				document.querySelectorAll('.ct-related-posts .entry-meta')
			).map((el) => {
				renderSingleEntryMeta({
					el,
					meta_divider: 'slash',
					meta_type: 'simple',
					meta_elements: getOptionFor(
						'related_single_meta_elements',
						prefix
					),
				})
			})
		}
	},
})
