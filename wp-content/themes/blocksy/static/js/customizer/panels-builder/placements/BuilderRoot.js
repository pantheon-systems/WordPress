import {
	createElement,
	Fragment,
	Component,
	useEffect,
	useRef,
	useMemo,
	createPortal,
	useState,
	useCallback,
	createContext,
	useReducer,
} from '@wordpress/element'
import PlacementsBuilder from './PlacementsBuilder'
import DraggableItems from './DraggableItems'
import ViewSwitch from './ViewSwitch'
import AvailableItems from './AvailableItems'
import { builderReducer } from './builderReducer'
import { useDeviceManager } from '../../components/useDeviceManager'
import ctEvents from 'ct-events'

export const DragDropContext = createContext({})

const getDocument = (x) =>
	x.document || x.contentDocument || x.contentWindow.document

export const fetchCurrentHeader = () => {
	const document = getDocument(
		wp.customize.previewer.container.find('iframe')[0]
	)

	if (
		wp.customize.previewer.container
			.find('iframe')[0]
			.contentDocument.querySelector('header#header')
	) {
		return wp.customize.previewer.container
			.find('iframe')[0]
			.contentDocument.querySelector('header#header').dataset.id
	}

	return null
}

const BuilderRoot = ({
	value: allBuilderSections,
	option,
	onChange: onBuilderValueChange,
}) => {
	const currentHeader = useRef(null)

	if (currentHeader.current === null) {
		currentHeader.current = (
			allBuilderSections.sections.find(
				({ id }) => id.indexOf(fetchCurrentHeader()) > -1
			) || allBuilderSections.sections[0]
		).id
	}

	useEffect(() => {
		let {
			__forced_dynamic_header__,
			__forced_static_header__,
			__should_refresh__,
			...old
		} = wp.customize('header_placements')()

		Object.keys(old).map((key) => {
			if (parseFloat(key)) {
				delete old[key]
			}
		})

		try {
			wp.customize('header_placements')({
				...old,
				__forced_static_header__: (
					allBuilderSections.sections.find(
						({ id }) => id.indexOf(fetchCurrentHeader()) > -1
					) || allBuilderSections.sections[0]
				).id,
			})
		} catch (e) {
			console.error(e)
		}

		return () => {
			const {
				__forced_dynamic_header__,
				__forced_static_header__,
				__should_refresh__,
				...old
			} = wp.customize('header_placements')()

			wp.customize('header_placements')({
				__should_refresh__: true,
				[Math.random()]: 'update',
				...old,
			})
		}
	}, [])

	const [isDragging, setIsDragging] = useState(false)

	const [builderValueCollection, builderValueDispatchInternal] = useReducer(
		builderReducer,
		{
			...allBuilderSections,
			...(currentHeader.current
				? {
						__forced_static_header__: currentHeader.current,
				  }
				: {}),
		}
	)

	const builderValue = useMemo(
		() =>
			builderValueCollection.sections.find(
				({ id }) =>
					id === builderValueCollection.__forced_static_header__
			) || builderValueCollection.sections[0],
		[builderValueCollection]
	)

	// desktop | mobile
	const [currentView, setCurrentView] = useDeviceManager({
		withTablet: false,
	})

	const inlinedItemsFromBuilder = useMemo(
		() =>
			builderValue[currentView].reduce(
				(currentItems, { id, placements }) => [
					...currentItems,
					...(placements || []).reduce(
						(c, { id, items }) => [...c, ...items],
						[]
					),
				],
				[]
			),
		[builderValue, currentView]
	)

	const builderValueDispatch = useCallback(
		(action) => {
			let newState = builderReducer(builderValueCollection, action)

			if (action.type === 'ITEM_VALUE_ON_CHANGE') {
				const {
					id,
					optionId,
					optionValue,
					values = {},
				} = action.payload

				const builderValue =
					newState.sections.find(
						({ id }) => id === newState.__forced_static_header__
					) || newState.sections[0]

				let items = builderValue.items

				if (
					id === 'logo' &&
					optionId === 'custom_logo' &&
					builderValue.id === 'type-1'
				) {
					wp.customize &&
						wp.customize('custom_logo')(
							optionValue
								? optionValue.desktop
									? optionValue.desktop
									: optionValue
								: ''
						)
				}

				wp.customize.previewer &&
					wp.customize.previewer.send(
						'ct:header:receive-value-update',
						{
							itemId: id,
							optionId,
							optionValue,
							futureItems: builderValue.items,
							values: {
								...(
									items.find(({ id: _id }) => id === _id) || {
										values: {},
									}
								).values,
								...values,
								[optionId]: optionValue,
							},
						}
					)
			}

			if (action.type === 'BUILDER_GLOBAL_SETTING_ON_CHANGE') {
				const { optionId, optionValue, values = {} } = action.payload

				const builderValue =
					newState.sections.find(
						({ id }) => id === newState.__forced_static_header__
					) || newState.sections[0]

				wp.customize.previewer &&
					wp.customize.previewer.send(
						'ct:header:receive-value-update',
						{
							itemId: 'global',
							optionId,
							optionValue,
							values: {
								...builderValue.settings,
								...values,
								[optionId]: optionValue,
							},
						}
					)
			}

			onBuilderValueChange(newState)

			builderValueDispatchInternal(action)
		},
		[
			builderValueDispatchInternal,
			onBuilderValueChange,
			builderValueCollection,
		]
	)

	const setList = useCallback(
		(lists) =>
			builderValueDispatch({
				type: 'SET_LIST',
				payload: {
					currentView,
					lists,
				},
			}),
		[builderValueDispatch, currentView]
	)

	return (
		<Fragment>
			<DragDropContext.Provider
				value={{
					option,
					currentView,
					isDragging,
					setIsDragging,
					setList,
					builderValueDispatch,
					builderValue,
					onChange: ({ id, value }) => setList({ [id]: value }),
					builderValueCollection,
				}}>
				<AvailableItems
					allBuilderSections={allBuilderSections}
					builderValue={builderValue}
					inlinedItemsFromBuilder={inlinedItemsFromBuilder}
					builderValueDispatch={builderValueDispatch}
				/>

				{createPortal(
					<div className="ct-builder-header">
						<ViewSwitch
							currentView={currentView}
							setCurrentView={setCurrentView}
						/>

						<PlacementsBuilder
							inlinedItemsFromBuilder={inlinedItemsFromBuilder}
							builderValueWithView={builderValue[currentView]}
							view={currentView}
						/>
					</div>,
					document.querySelector('.ct-panel-builder')
				)}
			</DragDropContext.Provider>
		</Fragment>
	)
}

export default BuilderRoot
