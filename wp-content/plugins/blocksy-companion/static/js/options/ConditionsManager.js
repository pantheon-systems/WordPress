import { createElement, useEffect, useState } from '@wordpress/element'
import cls from 'classnames'
import { __ } from 'ct-i18n'
import { Select } from 'blocksy-options'

import PostIdPicker from './ConditionsManager/PostIdPicker'

let allTaxonomiesCache = []
let allLanguagesCache = []
let allUsersCache = []

const ConditionsManager = ({ value, onChange, filter = 'all' }) => {
	let rulesToUse = blocksy_admin.all_condition_rules

	if (filter === 'singular') {
		rulesToUse = blocksy_admin.singular_condition_rules
	}

	if (filter === 'archive') {
		rulesToUse = blocksy_admin.archive_condition_rules
	}

	const allRules = rulesToUse
		.reduce(
			(current, { rules, title }) => [
				...current,
				...rules.map((r) => ({
					...r,
					group: title,
				})),
			],
			[]
		)
		.reduce(
			(current, { title, id, ...rest }) => [
				...current,
				{
					key: id,
					value: title,
					...rest,
				},
			],
			[]
		)

	const [allTaxonomies, setAllTaxonomies] = useState(allTaxonomiesCache)
	const [allLanguages, setAllLanguages] = useState(allLanguagesCache)
	const [allUsers, setAllUsers] = useState(allUsersCache)

	const hasAdditions = (condition) =>
		condition.rule === 'post_ids' ||
		condition.rule === 'page_ids' ||
		condition.rule === 'custom_post_type_ids' ||
		condition.rule === 'taxonomy_ids' ||
		condition.rule === 'post_with_taxonomy_ids' ||
		condition.rule === 'current_language' ||
		condition.rule === 'user_post_author_id' ||
		condition.rule === 'author'

	useEffect(() => {
		fetch(
			`${wp.ajax.settings.url}?action=blocksy_conditions_get_all_taxonomies`,
			{
				headers: {
					Accept: 'application/json',
					'Content-Type': 'application/json',
				},
				method: 'POST',
			}
		)
			.then((r) => r.json())
			.then(({ data: { taxonomies, languages, users } }) => {
				setAllTaxonomies(taxonomies)
				allTaxonomiesCache = taxonomies

				setAllLanguages(languages)
				allLanguagesCache = languages

				setAllUsers(users)
				allUsersCache = users
			})
	}, [])

	return (
		<div className="ct-display-conditions">
			{value.map((condition, index) => (
				<div
					className={cls('ct-condition-group', {
						'ct-cols-3': hasAdditions(condition),
						'ct-cols-2': !hasAdditions(condition),
					})}
					key={index}>
					<Select
						key="first"
						option={{
							inputClassName: 'ct-condition-type',
							selectInputStart: () => (
								<span className={`ct-${condition.type}`} />
							),
							placeholder: __(
								'Select variation',
								'blocksy-companion'
							),
							choices: {
								include: __('Include', 'blocksy-companion'),
								exclude: __('Exclude', 'blocksy-companion'),
							},
						}}
						value={condition.type}
						onChange={(type) => {
							onChange(
								value.map((r, i) => ({
									...(i === index
										? {
												...condition,
												type,
										  }
										: r),
								}))
							)
						}}
					/>

					<Select
						key="second"
						option={{
							appendToBody: true,
							placeholder: __('Select rule', 'blocksy-companion'),
							choices:
								condition.category === 'user'
									? allRules.filter(
											({ key }) =>
												key.indexOf('user_') === 0
									  )
									: allRules.filter(
											({ key }) =>
												key.indexOf('user_') === -1
									  ),
							search: true,
						}}
						value={condition.rule}
						onChange={(rule) => {
							onChange(
								value.map((r, i) => ({
									...(i === index
										? {
												...condition,
												rule,
										  }
										: r),
								}))
							)
						}}
					/>

					{(condition.rule === 'post_ids' ||
						condition.rule === 'custom_post_type_ids' ||
						condition.rule === 'page_ids') && (
						<PostIdPicker
							condition={condition}
							onChange={(post_id) => {
								onChange(
									value.map((r, i) => ({
										...(i === index
											? {
													...condition,
													payload: {
														...condition.payload,
														post_id,
													},
											  }
											: r),
									}))
								)
							}}
						/>
					)}

					{(condition.rule === 'taxonomy_ids' ||
						condition.rule === 'post_with_taxonomy_ids') && (
						<Select
							option={{
								appendToBody: true,
								defaultToFirstItem: false,
								placeholder: __(
									'Select taxonomy',
									'blocksy-companion'
								),
								choices: allTaxonomies.map((taxonomy) => ({
									key: taxonomy.id,
									value: taxonomy.name,
									...(taxonomy.group
										? { group: taxonomy.group }
										: {}),
								})),
								search: true,
							}}
							value={(condition.payload || {}).taxonomy_id || ''}
							onChange={(taxonomy_id) => {
								onChange(
									value.map((r, i) => ({
										...(i === index
											? {
													...condition,
													payload: {
														...condition.payload,
														taxonomy_id,
													},
											  }
											: r),
									}))
								)
							}}
						/>
					)}

					{condition.rule === 'current_language' && (
						<Select
							option={{
								appendToBody: true,
								defaultToFirstItem: false,
								placeholder: __(
									'Select language',
									'blocksy-companion'
								),
								choices: allLanguages.map((language) => ({
									key: language.id,
									value: language.name,
								})),
								search: true,
							}}
							value={(condition.payload || {}).language || ''}
							onChange={(language) => {
								onChange(
									value.map((r, i) => ({
										...(i === index
											? {
													...condition,
													payload: {
														...condition.payload,
														language,
													},
											  }
											: r),
									}))
								)
							}}
						/>
					)}

					{condition.rule === 'user_post_author_id' && (
						<Select
							option={{
								appendToBody: true,
								defaultToFirstItem: false,
								placeholder: __(
									'Select user',
									'blocksy-companion'
								),
								choices: [
									{
										key: 'current_user',
										value: __(
											'Current user',
											'blocksy-companion'
										),
									},

									...allUsers.map((user) => ({
										key: user.id,
										value: user.name,
									})),
								],
								search: true,
							}}
							value={(condition.payload || {}).user_id || ''}
							onChange={(user_id) => {
								onChange(
									value.map((r, i) => ({
										...(i === index
											? {
													...condition,
													payload: {
														...condition.payload,
														user_id,
													},
											  }
											: r),
									}))
								)
							}}
						/>
					)}

					{condition.rule === 'author' && (
						<Select
							option={{
								appendToBody: true,
								placeholder: __(
									'Select user',
									'blocksy-companion'
								),
								choices: [
									{
										key: 'all_users',
										value: __(
											'All authors',
											'blocksy-companion'
										),
									},

									...allUsers.map((user) => ({
										key: user.id,
										value: user.name,
									})),
								],
								search: true,
							}}
							value={(condition.payload || {}).user_id || 'all_users'}
							onChange={(user_id) => {
								onChange(
									value.map((r, i) => ({
										...(i === index
											? {
													...condition,
													payload: {
														...condition.payload,
														user_id,
													},
											  }
											: r),
									}))
								)
							}}
						/>
					)}

					<button
						type="button"
						onClick={(e) => {
							e.preventDefault()

							let newValue = [...value]
							newValue.splice(index, 1)

							onChange(newValue)
						}}>
						×
					</button>
				</div>
			))}

			<div className="ct-conditions-actions">
				<button
					type="button"
					className="button add-condition"
					onClick={(e) => {
						e.preventDefault()

						onChange([
							...value,
							{
								type: 'include',
								rule: rulesToUse[0].rules[0].id,
								payload: {},
							},
						])
					}}>
					{__('Add Display Condition', 'blocksy-companion')}
				</button>

				<button
					type="button"
					className="button add-condition"
					onClick={(e) => {
						e.preventDefault()

						onChange([
							...value,
							{
								type: 'include',
								rule: 'user_logged_in',
								payload: {},
								category: 'user',
							},
						])
					}}>
					{__('Add User Condition', 'blocksy-companion')}
				</button>
			</div>
		</div>
	)
}
export default ConditionsManager
