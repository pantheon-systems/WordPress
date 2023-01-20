import { colord } from 'colord'

export const normalizeColor = (color) => {
	const parsedColor = colord(color)

	if (!parsedColor.parsed) {
		return color
	}

	if (color[0] === '#' && color.length <= 7) {
		return color
	}

	if (parsedColor.rgba.a === 1) {
		return color
	}

	return parsedColor.toRgbString()
}
