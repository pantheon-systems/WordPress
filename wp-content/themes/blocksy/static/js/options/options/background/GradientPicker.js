import {
	Fragment,
	createElement,
	useRef,
	useEffect,
	useMemo,
	useCallback,
	useState,
} from '@wordpress/element'

import classnames from 'classnames'

import { GradientPicker as StableGradientPicker } from '@wordpress/components'

const GradientPicker = ({ value, onChange }) => {
	const allGradients = (window.ct_customizer_localizations ||
		window.ct_localizations)['gradients']

	return (
		<Fragment>
			<StableGradientPicker
				value={value.gradient || ''}
				onChange={(val) => {
					onChange({
						...value,
						gradient: val,
					})
				}}
			/>

			<ul className={'ct-gradient-swatches'}>
				{allGradients.map(({ gradient, slug }) => (
					<li
						onClick={() => {
							onChange({
								...value,
								gradient:
									value.gradient === gradient ? '' : gradient,
							})
						}}
						className={classnames({
							active: gradient === value.gradient,
						})}
						style={{
							'--background-image': gradient,
						}}
						key={slug}></li>
				))}
			</ul>
		</Fragment>
	)
}

export default GradientPicker
