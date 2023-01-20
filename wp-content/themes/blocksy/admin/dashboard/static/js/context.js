import { createContext } from '@wordpress/element'

export const getDefaultValue = () => ({
	theme_version: '1.0.0',
	theme_name: 'Word'
})

const DashboardContext = createContext(getDefaultValue())

export const Provider = DashboardContext.Provider
export const Consumer = DashboardContext.Consumer

export default DashboardContext
