import {
	createElement,
	Component,
	useState,
	useEffect,
	Fragment,
} from '@wordpress/element'
import Downshift from 'downshift'
import { __ } from 'ct-i18n'
import classnames from 'classnames'

let listsCache = null

const ListPickerImplementation = ({ value, onChange }) => {
	const [lists, setLists] = useState(listsCache || [])
	const [isLoadingLists, setListsLoading] = useState(!listsCache)

	const maybeFetchLists = async (verbose = true) => {
		if (verbose) {
			setListsLoading(true)
		}

		const body = new FormData()
		body.append(
			'action',
			'blocksy_ext_newsletter_subscribe_get_actual_lists'
		)

		try {
			const response = await fetch(ajaxurl, {
				method: 'POST',
				body,
			})

			if (response.status === 200) {
				const body = await response.json()

				if (body.success) {
					if (body.data.result !== 'api_key_invalid') {
						setListsLoading(false)
						setLists(body.data.result)
						listsCache = body.data.result

						return
					}
				}
			}
		} catch (e) {}

		setListsLoading(false)
	}

	useEffect(() => {
		maybeFetchLists(!listsCache)
	}, [])

	return lists.length === 0 ? (
		<div className="ct-select-input">
			<input
				disabled
				placeholder={
					isLoadingLists
						? __('Loading...', 'blocksy-companion')
						: __('Invalid API Key...', 'blocksy-companion')
				}
			/>
		</div>
	) : (
		<Downshift
			selectedItem={value || lists[0].id}
			onChange={(selection) => onChange(selection)}
			itemToString={(item) =>
				item ? (lists.find(({ id }) => id === item) || {}).name : ''
			}>
			{({
				getInputProps,
				getItemProps,
				getLabelProps,
				getMenuProps,
				isOpen,
				inputValue,
				highlightedIndex,
				selectedItem,
				openMenu,
			}) => (
				<div className="ct-select-input">
					<input
						{...getInputProps({
							onFocus: () => openMenu(),
							onClick: () => openMenu(),
						})}
						placeholder={__('Select list...', 'blocksy-companion')}
						readOnly
					/>

					{isOpen && (
						<div
							{...getMenuProps({
								className: 'ct-select-dropdown',
							})}>
							{lists.map((item, index) => (
								<div
									{...getItemProps({
										key: item.id,
										index,
										item: item.id,
										className: classnames(
											'ct-select-dropdown-item',
											{
												active:
													highlightedIndex === index,
												selected:
													selectedItem === item.id,
											}
										),
									})}>
									{item.name}
								</div>
							))}
						</div>
					)}
				</div>
			)}
		</Downshift>
	)
}

export default ListPickerImplementation
