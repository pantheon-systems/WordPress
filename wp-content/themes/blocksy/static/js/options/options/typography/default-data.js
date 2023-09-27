import { __ } from 'ct-i18n'

export const getDefaultFonts = ({ isDefault }) => ({
	system: {
		type: 'system',
		families: [
			...(!isDefault ? ['Default'] : []),
			'System Default',
			'Arial',
			'Verdana',
			'Trebuchet',
			'Georgia',
			'Times New Roman',
			'Palatino',
			'Helvetica',
			'Myriad Pro',
			'Lucida',
			'Gill Sans',
			'Impact',
			'Serif',
			'monospace',
		].map((family) => ({
			source: 'system',
			family,
			display:
				family === 'System Default'
					? __('System Default', 'blocksy')
					: family,
			variations: [],
			all_variations: [
				...(family === 'Default' ? ['Default'] : []),
				'n1',
				'i1',
				'n2',
				'i2',
				'n3',
				'i3',
				'n4',
				'i4',
				'n5',
				'i5',
				'n6',
				'i6',
				'n7',
				'i7',
				'n8',
				'i8',
				'n9',
				'i9',
			],
		})),
	},
})
