export const customItemsSeparator = () => '~'
export const getOriginalId = (id) => id.split('~')[0]

export const shortenItemId = (id) => {
	let components = id.split(customItemsSeparator())

	if (components.length === 1) {
		return components[0]
	}

	return components[1].substring(0, 6)
}
