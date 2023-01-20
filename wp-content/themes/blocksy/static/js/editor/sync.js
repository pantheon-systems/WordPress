import ctEvents from 'ct-events'
import { select, useSelect } from '@wordpress/data'
import {
	updateVariableInStyleTags,
	overrideStylesWithAst,
} from 'customizer-sync-helpers'
import { getValueFromInput } from 'blocksy-options'
import { gutenbergVariables } from './variables'

let oldFn =
	wp.data.dispatch('core/edit-post').__experimentalSetPreviewDeviceType

let oldFnToggleFeature = wp.data.dispatch('core/edit-post').toggleFeature

const performSelectorsReplace = () => {
	let googleFontsUrl = ''
	let maybeGlobalStyles = document.querySelector('#ct-main-styles-inline-css')

	if (
		maybeGlobalStyles &&
		maybeGlobalStyles.innerText.indexOf('googleapis.com') > -1
	) {
		let googleFonts = maybeGlobalStyles.innerText.split('display=swap')

		googleFontsUrl =
			googleFonts[0].trim().replace("@import url('", '') + 'display=swap'
	}

	;[...document.querySelectorAll('style')].map((style) => {
		if (!style.innerText) {
			return
		}

		if (style.innerText.indexOf('narrow-container-max-width') === -1) {
			return
		}

		style.innerText = style.innerText.replace(
			/\.editor-styles-wrapper \.edit-post-visual-editor__content-area \> div/g,
			'.edit-post-visual-editor__content-area > div'
		)

		style.innerText = style.innerText.replace(
			'.editor-styles-wrapperroot',
			':root'
		)
	})

	const maybeIframe = document.querySelector(
		'.edit-post-visual-editor__content-area iframe[name="editor-canvas"]'
	)

	if (maybeIframe) {
		;[...maybeIframe.contentDocument.querySelectorAll('style')].map(
			(style) => {
				if (
					style.innerText.indexOf('narrow-container-max-width') === -1
				) {
					return
				}

				if (googleFontsUrl) {
					if (style.innerHTML.indexOf(googleFontsUrl) === -1) {
						style.innerHTML = `@import url('${googleFontsUrl}');${style.innerHTML}`
					}
				}

				style.innerHTML = style.innerHTML.replace(
					/\.editor-styles-wrapper \.edit-post-visual-editor__content-area \> div/g,
					':root'
				)

				style.innerHTML = style.innerHTML.replace(
					/\.edit-post-visual-editor__content-area \> div/g,
					':root'
				)

				style.innerHTML = style.innerHTML.replace(
					'.editor-styles-wrapperroot',
					':root'
				)
			}
		)
	}
}

const performThemeEditorStylesUpdate = () => {
	setTimeout(() => {
		const themeStyles =
			select('core/edit-post').isFeatureActive('themeStyles')

		document.body.classList.remove('ct-theme-editor-styles')

		if (themeStyles) {
			document.body.classList.add('ct-theme-editor-styles')
		}
	})
}

if (oldFn) {
	setTimeout(() => {
		performSelectorsReplace()
		performThemeEditorStylesUpdate()
	}, 1000)

	wp.data.dispatch('core/edit-post').__experimentalSetPreviewDeviceType = (
		...args
	) => {
		oldFn(...args)
		setTimeout(() => {
			overrideStylesWithAst()
			performSelectorsReplace()
		}, 200)
	}

	wp.data.dispatch('core/edit-post').toggleFeature = (...args) => {
		oldFnToggleFeature(...args)
		performThemeEditorStylesUpdate()
	}
}

const unsubscribe = wp.data.subscribe(() => {
	const themeStyles = select('core/edit-post').isFeatureActive('themeStyles')

	document.body.classList.remove('ct-theme-editor-styles')

	if (themeStyles) {
		document.body.classList.add('ct-theme-editor-styles')
	}

	unsubscribe()
})

const syncContentBlocks = ({ atts }) => {
	let page_structure_type = atts.content_block_structure || 'type-4'

	document.body.classList.remove('ct-structure-narrow', 'ct-structure-normal')

	if (
		(atts.has_content_block_structure &&
			atts.has_content_block_structure !== 'yes') ||
		atts.template_subtype === 'content'
	) {
		document.body.classList.add(`ct-structure-normal`)
		return
	}

	document.body.classList.add(
		`ct-structure-${page_structure_type === 'type-4' ? 'normal' : 'narrow'}`
	)
}

export const mountSync = (atts = {}) => {
	atts = {
		...(select('core/editor').getEditedPostAttribute('blocksy_meta') || {}),
		...atts,
	}

	if (document.body.classList.contains('post-type-ct_content_block')) {
		syncContentBlocks({ atts })
		return
	}

	let page_structure_type = atts.page_structure_type || 'default'

	if (page_structure_type === 'default') {
		page_structure_type = ct_editor_localizations.default_page_structure
	}

	document.body.classList.remove('ct-structure-narrow', 'ct-structure-normal')

	document.body.classList.add(
		`ct-structure-${page_structure_type === 'type-4' ? 'normal' : 'narrow'}`
	)
}

export const handleMetaboxValueChange = (optionId, optionValue) => {
	if (
		optionId === 'page_structure_type' ||
		optionId === 'has_content_block_structure' ||
		optionId === 'content_block_structure' ||
		optionId === 'template_subtype'
	) {
		mountSync({
			[optionId]: optionValue,
		})
	}

	const atts = {
		...getValueFromInput(
			ct_editor_localizations.post_options,
			wp.data
				.select('core/editor')
				.getEditedPostAttribute('blocksy_meta') || {}
		),
		[optionId]: optionValue,
	}

	if (gutenbergVariables[optionId]) {
		updateVariableInStyleTags({
			variableDescriptor: Array.isArray(gutenbergVariables[optionId])
				? gutenbergVariables[optionId]
				: [gutenbergVariables[optionId]],

			value: optionValue,
			fullValue: atts,
			tabletMQ: '(max-width: 800px)',
			mobileMQ: '(max-width: 370px)',
		})

		performSelectorsReplace()
	}
}
