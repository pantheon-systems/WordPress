import {
	createElement,
	Component,
	useEffect,
	useState,
	Fragment,
} from '@wordpress/element'
import Downshift from 'downshift'
import { __ } from 'ct-i18n'
import classnames from 'classnames'

const ListPicker = ({ listId, provider, apiKey, onChange }) => {
	const [lists, setLists] = useState([])
	const [isLoadingLists, setListsLoading] = useState(false)

	let [{ controller }, setAbortState] = useState({
		controller: null,
	})

	const maybeFetchLists = async () => {
		if (controller) {
			controller.abort()
		}

		setListsLoading(true)

		if ('AbortController' in window) {
			controller = new AbortController()

			setAbortState({
				controller,
			})
		}

		const body = new FormData()

		body.append('api_key', apiKey)
		body.append('provider', provider)
		body.append(
			'action',
			'blocksy_ext_newsletter_subscribe_maybe_get_lists'
		)

		try {
			const response = await fetch(ctDashboardLocalizations.ajax_url, {
				method: 'POST',
				signal: controller.signal,
				body,
			})

			if (response.status === 200) {
				const body = await response.json()

				if (body.success) {
					if (body.data.result !== 'api_key_invalid') {
						setListsLoading(false)
						setLists(body.data.result)

						return
					}
				}
			}
		} catch (e) {}

		setLists([])
		setListsLoading(false)
	}

	useEffect(() => {
		if (!apiKey) {
			setLists([])
			return
		}

		maybeFetchLists()
	}, [provider, apiKey])

	return lists.length === 0 ? (
		<div className="ct-select-input">
			<input
				disabled
				placeholder={
					isLoadingLists
						? __('Loading', 'blocksy-companion')
						: __('Invalid API Key...', 'blocksy-companion')
				}
			/>
		</div>
	) : (
		<Downshift
			selectedItem={listId || ''}
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

export default ListPicker
