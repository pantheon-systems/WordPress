import {
	Fragment,
	createElement,
	Component,
	useEffect,
	useRef,
	useState,
} from '@wordpress/element'
import classnames from 'classnames'
import { getDefaultFonts } from './default-data'
import {
	familyForDisplay,
	humanizeVariations,
	fontFamilyToCSSFamily,
	humanizeFontSource,
} from './helpers'
import { FixedSizeList as List } from 'react-window'
import WebFontLoader from 'webfontloader'
import AutoSizer from 'react-virtualized-auto-sizer'

import { __ } from 'ct-i18n'

let loadedFonts = []

const loadGoogleFonts = (font_families) => {
	if (font_families.length === 0) return

	loadedFonts = [...loadedFonts, ...font_families.map(({ family }) => family)]

	const googleFonts = font_families
		.map(({ family }) => family)
		.filter((family) => family.indexOf('ct_typekit') === -1)

	const typekitFonts = font_families.filter(
		({ family }) => family.indexOf('ct_typekit') > -1
	)

	if (googleFonts.length > 0 || typekitFonts.length > 0) {
		WebFontLoader.load({
			...(googleFonts.length > 0
				? {
						google: {
							families: googleFonts,
						},
				  }
				: {}),
			...(typekitFonts.length > 0
				? {
						typekit: {
							id: typekitFonts[0].kit,
						},
				  }
				: {}),
			classes: false,
			text: 'abcdefghijklmnopqrstuvwxyz',
		})
	}
}

const SingleFont = ({
	data: { linearFontsList, onPickFamily, value },
	index,
	style,
}) => {
	const family = linearFontsList[index]

	return (
		<div
			style={style}
			onClick={() => onPickFamily(family)}
			className={classnames(
				'ct-typography-single-font',
				`ct-${family.source}`,
				{
					active: family.family === value.family,
				}
			)}
			key={family.family}>
			<span className="ct-font-name">
				<span
					className={`ct-font-type-${family.source}`}
					title={humanizeFontSource(family.source)}>
					{humanizeFontSource(family.source)[0]}
				</span>
				
				{familyForDisplay(family.display || family.family)}

				{family.variable && <i>({__('Variable', 'blocksy')})</i>}
			</span>
			<span
				style={{
					fontFamily: fontFamilyToCSSFamily(family.family),
				}}
				className="ct-font-preview">
				Simply dummy text
			</span>
		</div>
	)
}

const FontsList = ({
	option,
	value,
	onPickFamily,
	typographyList,
	linearFontsList,
	currentView,
	searchTerm,
}) => {
	const listRef = useRef(null)
	const timerRef = useRef(null)
	const [scrollTimer, setScrollTimer] = useState(null)

	useEffect(() => {
		if (value.family) {
			listRef.current.scrollToItem(
				linearFontsList
					.map(({ family }) => family)
					.indexOf(value.family),
				'start'
			)
		}
	}, [])

	const onScroll = () => {
		scrollTimer && clearTimeout(scrollTimer)

		setScrollTimer(
			setTimeout(() => {
				if (!listRef.current) {
					return
				}

				const [overscanStartIndex] = listRef.current._getRangeToRender()

				const perPage = 25

				const totalPages = Math.ceil(linearFontsList.length / perPage)
				const startingPage = Math.ceil(
					(overscanStartIndex + 1) / perPage
				)
				// const stopPage = Math.ceil((overscanStopIndex + 1) / perPage)

				const pageItems = [...Array(perPage)]
					.map((_, i) => (startingPage - 1) * perPage + i)
					.map((index) => linearFontsList[index])
					.filter((s) => !!s)
					.filter(
						({ source, family }) =>
							loadedFonts.indexOf(family) === -1 &&
							(source === 'google' || source === 'typekit')
					)

				loadGoogleFonts(pageItems)
			}, 100)
		)
	}

	useEffect(() => {
		onScroll()
	}, [linearFontsList])

	return (
		<List
			height={360}
			itemCount={linearFontsList.length}
			itemSize={85}
			ref={listRef}
			onScroll={(e) => {
				onScroll()
			}}
			itemData={{
				linearFontsList,
				onPickFamily,
				value,
			}}
			onItemsRendered={({ overscanStartIndex, overscanStopIndex }) => {}}
			className="ct-typography-fonts">
			{SingleFont}
		</List>
	)
}

export default FontsList
