import { createElement } from '@wordpress/element'
import BuilderRoot from '../../customizer/panels-builder/placements/BuilderRoot'

const HeaderBuilder = props => <BuilderRoot {...props} />
HeaderBuilder.renderingConfig = { design: 'none' }

export default HeaderBuilder
