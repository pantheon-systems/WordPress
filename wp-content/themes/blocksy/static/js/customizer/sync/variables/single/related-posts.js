import {
	applyPrefixFor,
	handleResponsiveSwitch,
	getPrefixFor,
} from '../../helpers'
import { handleBackgroundOptionFor } from '../../variables/background'
import { typographyOption } from '../../variables/typography'
import { getSingleShareBoxVariables } from './share-box'

import { maybePromoteScalarValueIntoResponsive } from 'customizer-sync-helpers/dist/promote-into-responsive'

let prefix = getPrefixFor()

export const getSingleElementsVariables = () => ({
	...getSingleShareBoxVariables(),

	// Autor Box
	[`${prefix}_single_author_box_spacing`]: {
		selector: applyPrefixFor('.author-box', prefix),
		variable: 'spacing',
		responsive: true,
		unit: '',
	},

	...typographyOption({
		id: `${prefix}_single_author_box_name_font`,
		selector: applyPrefixFor('.author-box .author-box-name', prefix),
	}),

	[`${prefix}_single_author_box_name_color`]: {
		selector: applyPrefixFor('.author-box .author-box-name', prefix),
		variable: 'heading-color',
		type: 'color:default',
		responsive: true,
	},

	...typographyOption({
		id: `${prefix}_single_author_box_font`,
		selector: applyPrefixFor('.author-box .author-box-bio', prefix),
	}),

	[`${prefix}_single_author_box_font_color`]: [
		{
			selector: applyPrefixFor('.author-box .author-box-bio', prefix),
			variable: 'color',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: applyPrefixFor('.author-box .author-box-bio', prefix),
			variable: 'linkInitialColor',
			type: 'color:initial',
			responsive: true,
		},

		{
			selector: applyPrefixFor('.author-box .author-box-bio', prefix),
			variable: 'linkHoverColor',
			type: 'color:hover',
			responsive: true,
		},
	],

	[`${prefix}_single_author_box_social_icons_color`]: [
		{
			selector: applyPrefixFor('.author-box .author-box-social', prefix),
			variable: 'icon-color',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: applyPrefixFor('.author-box .author-box-social', prefix),
			variable: 'icon-hover-color',
			type: 'color:hover',
			responsive: true,
		},
	],

	[`${prefix}_single_author_box_social_icons_background`]: [
		{
			selector: applyPrefixFor('.author-box .author-box-social', prefix),
			variable: 'background-color',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: applyPrefixFor('.author-box .author-box-social', prefix),
			variable: 'background-hover-color',
			type: 'color:hover',
			responsive: true,
		},
	],

	...handleBackgroundOptionFor({
		id: `${prefix}_single_author_box_container_background`,
		selector: applyPrefixFor(
			'.author-box[data-type="type-1"]',
			prefix
		),
		responsive: true,
	}),

	[`${prefix}_single_author_box_shadow`]: {
		selector: applyPrefixFor('.author-box[data-type="type-1"]', prefix),
		type: 'box-shadow',
		variable: 'box-shadow',
		responsive: true,
	},

	[`${prefix}_single_author_box_container_border`]: {
		selector: applyPrefixFor('.author-box[data-type="type-1"]', prefix),
		variable: 'border',
		type: 'border',
		responsive: true,
		// skip_none: true,
	},

	[`${prefix}_single_author_box_border_radius`]: {
		selector: applyPrefixFor('.author-box[data-type="type-1"]', prefix),
		type: 'spacing',
		variable: 'border-radius',
		responsive: true,
	},

	[`${prefix}_single_author_box_border`]: {
		selector: applyPrefixFor('.author-box[data-type="type-2"]', prefix),
		variable: 'border-color',
		type: 'color',
		responsive: true,
	},

	[`${prefix}_related_label_alignment`]: {
		selector: applyPrefixFor('.ct-related-posts .ct-block-title', prefix),
		variable: 'horizontal-alignment',
		responsive: true,
		unit: '',
	},

	...handleBackgroundOptionFor({
		id: `${prefix}_related_posts_background`,
		selector: applyPrefixFor('.ct-related-posts-container', prefix),
	}),

	[`${prefix}_related_posts_container_spacing`]: {
		selector: applyPrefixFor('.ct-related-posts-container', prefix),
		variable: 'padding',
		responsive: true,
		unit: '',
	},

	[`${prefix}_related_posts_label_color`]: {
		selector: applyPrefixFor('.ct-related-posts .ct-block-title', prefix),
		variable: 'heading-color',
		type: 'color:default',
	},

	[`${prefix}_related_posts_link_color`]: [
		{
			selector: applyPrefixFor('.related-entry-title', prefix),
			variable: 'heading-color',
			type: 'color:default',
		},

		{
			selector: applyPrefixFor('.related-entry-title', prefix),
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_related_posts_meta_color`]: [
		{
			selector: applyPrefixFor('.ct-related-posts .entry-meta', prefix),
			variable: 'color',
			type: 'color:default',
		},

		{
			selector: applyPrefixFor('.ct-related-posts .entry-meta', prefix),
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_related_thumb_radius`]: {
		selector: applyPrefixFor(
			'.ct-related-posts .ct-image-container',
			prefix
		),
		type: 'spacing',
		variable: 'borderRadius',
		responsive: true,
	},

	[`${prefix}_related_narrow_width`]: {
		selector: applyPrefixFor('.ct-related-posts-container', prefix),
		variable: 'narrow-container-max-width',
		unit: 'px',
	},

	[`${prefix}_related_posts_columns`]: [
		{
			selector: applyPrefixFor('.ct-related-posts', prefix),
			variable: 'grid-template-columns',
			responsive: true,
			extractValue: (val) => {
				const responsive = maybePromoteScalarValueIntoResponsive(val)

				return {
					desktop: `repeat(${responsive.desktop}, 1fr)`,
					tablet: `repeat(${responsive.tablet}, 1fr)`,
					mobile: `repeat(${responsive.mobile}, 1fr)`,
				}
			},
		},
	],

	// Posts Navigation
	[`${prefix}_post_nav_spacing`]: {
		selector: applyPrefixFor('.post-navigation', prefix),
		variable: 'margin',
		responsive: true,
		unit: '',
	},

	[`${prefix}_posts_nav_font_color`]: [
		{
			selector: applyPrefixFor('.post-navigation', prefix),
			variable: 'linkInitialColor',
			type: 'color:default',
		},

		{
			selector: applyPrefixFor('.post-navigation', prefix),
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	[`${prefix}_posts_nav_image_overlay_color`]: {
		selector: applyPrefixFor('.post-navigation', prefix),
		variable: 'image-overlay-color',

		type: 'color:hover',
	},

	[`${prefix}_posts_nav_image_border_radius`]: {
		selector: applyPrefixFor('.post-navigation figure', prefix),
		type: 'spacing',
		variable: 'border-radius',
		// responsive: true,
	},
})
