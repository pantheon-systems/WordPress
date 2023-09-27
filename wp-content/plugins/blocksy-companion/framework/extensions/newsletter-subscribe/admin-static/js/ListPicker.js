import { createElement, Component } from '@wordpress/element'
import { __ } from 'ct-i18n'
import { lazy, Suspense } from 'react'

const ListPickerImplementation = lazy(() =>
	import('./ListPicker/Implementation')
)

const ListPicker = (props) => (
	<div>
		<Suspense
			fallback={
				<div className="ct-select-input">
					<input disabled placeholder={__('Loading...', 'blocksy-companion')} />
				</div>
			}>
			<ListPickerImplementation {...props} />
		</Suspense>
	</div>
)

export default ListPicker
