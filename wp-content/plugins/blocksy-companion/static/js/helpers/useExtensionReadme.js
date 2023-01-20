import {
	createElement,
	Component,
	useEffect,
	useState,
	Fragment
} from '@wordpress/element'
import { Dialog, DialogOverlay, DialogContent } from './reach/dialog'
// import '@reach/dialog/styles.css'
import Overlay from './Overlay'

const useExtensionReadme = extension => {
	const [showReadme, setIsShowingReadme] = useState(false)

	return [
		() => setIsShowingReadme(true),

		<Overlay
			items={showReadme}
			onDismiss={() => setIsShowingReadme(false)}
			render={() => (
				<div
					className="ct-modal-content"
					dangerouslySetInnerHTML={{
						__html: extension.readme
					}}
				/>
			)}
		/>
	]
}

export default useExtensionReadme
