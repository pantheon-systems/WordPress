import { createElement } from '@wordpress/element'
import LegacyWidgetArea from './widget-area/LegacyWidgetArea'
import NewBlockWidgetArea from './widget-area/NewBlockWidgetArea'

const WidgetArea = ({ ...props }) => {
	let hasBlockWidgets = ct_customizer_localizations.has_new_widgets

	if (hasBlockWidgets) {
		return <NewBlockWidgetArea key={props.option.sidebarId} {...props} />
	}

	return <LegacyWidgetArea {...props} />
}

WidgetArea.renderingConfig = { design: 'none' }

export default WidgetArea
