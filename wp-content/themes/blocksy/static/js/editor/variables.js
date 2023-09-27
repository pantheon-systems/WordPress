import { handleBackgroundOptionFor } from '../customizer/sync/variables/background'
import { withKeys } from '../customizer/sync/helpers'
import { maybePromoteScalarValueIntoResponsive } from 'customizer-sync-helpers/dist/promote-into-responsive'

const isContentBlock = document.body.classList.contains(
	'post-type-ct_content_block'
)

export const gutenbergVariables = {
	...handleBackgroundOptionFor({
		id: 'background',
		selector: '.edit-post-visual-editor__content-area > div',
		responsive: true,
		addToDescriptors: {
			fullValue: true,
			important: true,
		},
		valueExtractor: ({ background }) => {
			if (
				!background.desktop &&
				!isContentBlock &&
				background.background_type === 'color' &&
				background.backgroundColor.default.color &&
				background.backgroundColor.default.color.indexOf(
					'CT_CSS_SKIP_RULE'
				) > -1
			) {
				return ct_editor_localizations.default_background
			}

			return background
		},
	}),

	...withKeys(
		[
			'content_style_source',
			'content_style',
			'content_background',
			'content_boxed_shadow',
			'boxed_content_spacing',
			'content_boxed_radius',

			...(isContentBlock
				? [
						'has_content_block_structure',
						'template_subtype',
						'template_editor_width_source',
						'template_editor_width',
				  ]
				: []),
		],
		[
			{
				selector: `.editor-styles-wrapper`,
				variable: 'block-max-width',
				extractValue: ({
					template_subtype,
					template_editor_width_source = 'small',
					template_editor_width = 1290,
				}) => {
					if (!template_subtype) {
						return 'CT_CSS_SKIP_RULE'
					}

					if (template_subtype !== 'card') {
						return 'CT_CSS_SKIP_RULE'
					}

					if (template_editor_width_source === 'small') {
						return 500
					}

					if (template_editor_width_source === 'medium') {
						return 900
					}

					return template_editor_width
				},
				fullValue: true,
				unit: 'px',
				important: true,
			},

			{
				selector: `.editor-styles-wrapper`,
				variable: 'has-boxed',
				responsive: true,
				extractValue: ({
					template_subtype,
					content_style_source = 'inherit',
					has_content_block_structure = 'yes',
					content_style = 'wide',
				}) => {
					if (!isContentBlock && content_style_source === 'inherit') {
						content_style =
							ct_editor_localizations.default_content_style
					}

					content_style =
						maybePromoteScalarValueIntoResponsive(content_style)

					if (
						isContentBlock &&
						(has_content_block_structure !== 'yes' ||
							template_subtype === 'card' ||
							template_subtype === 'content')
					) {
						content_style = {
							desktop: 'wide',
							tablet: 'wide',
							mobile: 'wide',
						}
					}

					return {
						desktop:
							content_style.desktop === 'boxed'
								? 'var(--true)'
								: 'var(--false)',

						tablet:
							content_style.tablet === 'boxed'
								? 'var(--true)'
								: 'var(--false)',

						mobile:
							content_style.mobile === 'boxed'
								? 'var(--true)'
								: 'var(--false)',
					}
				},
				fullValue: true,
				unit: '',
			},

			{
				selector: `.editor-styles-wrapper`,
				variable: 'has-wide',
				responsive: true,
				extractValue: ({
					template_subtype,
					has_content_block_structure = 'yes',
					content_style_source = 'inherit',
					content_style = 'wide',
				}) => {
					if (!isContentBlock && content_style_source === 'inherit') {
						content_style =
							ct_editor_localizations.default_content_style
					}

					content_style =
						maybePromoteScalarValueIntoResponsive(content_style)

					if (
						isContentBlock &&
						(has_content_block_structure !== 'yes' ||
							template_subtype === 'card' ||
							template_subtype === 'content')
					) {
						content_style = {
							desktop: 'wide',
							tablet: 'wide',
							mobile: 'wide',
						}
					}

					return {
						desktop:
							content_style.desktop === 'wide'
								? 'var(--true)'
								: 'var(--false)',

						tablet:
							content_style.tablet === 'wide'
								? 'var(--true)'
								: 'var(--false)',

						mobile:
							content_style.mobile === 'wide'
								? 'var(--true)'
								: 'var(--false)',
					}
				},
				fullValue: true,
				unit: '',
			},

			...handleBackgroundOptionFor({
				id: 'background',
				selector: '.editor-styles-wrapper',
				responsive: true,
				conditional_var: '--has-boxed',
				addToDescriptors: {
					fullValue: true,
				},
				valueExtractor: ({
					template_subtype,
					has_content_block_structure = 'yes',
					content_style_source = 'inherit',
					content_background,
				}) => {
					if (!isContentBlock && content_style_source === 'inherit') {
						content_background =
							ct_editor_localizations.default_content_background
					}

					if (
						isContentBlock &&
						(has_content_block_structure !== 'yes' ||
							template_subtype === 'card' ||
							template_subtype === 'content')
					) {
						content_background = JSON.parse(
							JSON.stringify(
								maybePromoteScalarValueIntoResponsive(
									content_background
								)
							)
						)

						content_background.desktop.background_type = 'color'
						content_background.desktop.backgroundColor.default.color =
							'CT_CSS_SKIP_RULE'

						content_background.tablet.background_type = 'color'
						content_background.tablet.backgroundColor.default.color =
							'CT_CSS_SKIP_RULE'

						content_background.mobile.background_type = 'color'
						content_background.mobile.backgroundColor.default.color =
							'CT_CSS_SKIP_RULE'
					}

					return content_background
				},
			}).background,

			{
				selector: '.editor-styles-wrapper',
				type: 'spacing',
				variable: 'boxed-content-spacing',
				responsive: true,
				unit: '',
				fullValue: true,
				extractValue: ({
					template_subtype,
					content_style_source = 'inherit',
					boxed_content_spacing,
					has_content_block_structure = 'yes',
				}) => {
					if (!isContentBlock && content_style_source === 'inherit') {
						boxed_content_spacing =
							ct_editor_localizations.default_boxed_content_spacing
					}

					if (
						isContentBlock &&
						(has_content_block_structure !== 'yes' ||
							template_subtype === 'card' ||
							template_subtype === 'content')
					) {
						return 'CT_CSS_SKIP_RULE'
					}

					return boxed_content_spacing
				},
			},

			{
				selector: '.editor-styles-wrapper',
				type: 'spacing',
				variable: 'border-radius',
				responsive: true,

				fullValue: true,
				extractValue: ({
					template_subtype,
					content_style_source = 'inherit',
					content_boxed_radius,
					has_content_block_structure = 'yes',
				}) => {
					if (!isContentBlock && content_style_source === 'inherit') {
						content_boxed_radius =
							ct_editor_localizations.default_content_boxed_radius
					}

					if (
						isContentBlock &&
						(has_content_block_structure !== 'yes' ||
							template_subtype === 'card' ||
							template_subtype === 'content')
					) {
						return 'CT_CSS_SKIP_RULE'
					}

					return content_boxed_radius
				},
			},

			{
				selector: '.editor-styles-wrapper',
				type: 'box-shadow',
				variable: 'box-shadow',
				responsive: true,
				fullValue: true,
				extractValue: ({
					template_subtype,
					content_style_source = 'inherit',
					content_boxed_shadow,
					has_content_block_structure = 'yes',
				}) => {
					if (!isContentBlock && content_style_source === 'inherit') {
						content_boxed_shadow =
							ct_editor_localizations.default_content_boxed_shadow
					}

					if (
						isContentBlock &&
						(has_content_block_structure !== 'yes' ||
							template_subtype === 'card' ||
							template_subtype === 'content')
					) {
						return 'CT_CSS_SKIP_RULE'
					}

					return content_boxed_shadow
				},
			},
		]
	),
}
