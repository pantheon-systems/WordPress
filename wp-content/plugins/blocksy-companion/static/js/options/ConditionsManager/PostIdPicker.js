import { createElement, useEffect, useState, useMemo } from '@wordpress/element'
import { Select } from 'blocksy-options'
import { __ } from 'ct-i18n'

const withUniqueIDs = (data) =>
	data.filter(
		(value, index, self) =>
			self.findIndex((m) => m.ID === value.ID) === index
	)

let allPostsCache = []

const PostIdPicker = ({ condition, onChange }) => {
	const [allPosts, setAllPosts] = useState(allPostsCache)

	const postTypeToDisplay = useMemo(
		() =>
			({
				post_ids: 'post',
				page_ids: 'page',
				custom_post_type_ids: 'ct_cpt',
			}[condition.rule]),
		[condition.rule]
	)

	const currentPostId = useMemo(
		() => (condition.payload || {}).post_id || '',
		[condition.payload && condition.payload.post_id]
	)

	const fetchPosts = (searchQuery = '') => {
		fetch(
			`${wp.ajax.settings.url}?action=blocksy_conditions_get_all_posts`,
			{
				headers: {
					Accept: 'application/json',
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					post_type: postTypeToDisplay,

					...(searchQuery ? { search_query: searchQuery } : {}),
					...(currentPostId ? { alsoInclude: currentPostId } : {}),
				}),
				method: 'POST',
			}
		)
			.then((r) => r.json())
			.then(({ data: { posts } }) => {
				setAllPosts((allPosts) =>
					withUniqueIDs([...allPosts, ...posts])
				)

				allPostsCache = withUniqueIDs([...allPostsCache, ...posts])
			})
	}

	useEffect(() => {
		fetchPosts()
	}, [postTypeToDisplay])

	return (
		<Select
			option={{
				appendToBody: true,
				defaultToFirstItem: false,
				searchPlaceholder: __(
					'Type to search by ID or title...',
					'blocksy-companion'
				),
				placeholder:
					condition.rule === 'post_ids'
						? __('Select post', 'blocksy-companion')
						: condition.rule === 'page_ids'
						? __('Select page', 'blocksy-companion')
						: __('Custom Post Type ID', 'blocksy-companion'),
				choices: [
					...allPosts
						.filter(({ post_type }) =>
							postTypeToDisplay === 'ct_cpt'
								? post_type !== 'post' && post_type !== 'page'
								: postTypeToDisplay === post_type
						)
						.map((post) => ({
							key: post.ID,
							value: post.post_title,
						})),
				],
				search: true,
			}}
			value={currentPostId}
			onChange={(post_id) => onChange(post_id)}
			onInputValueChange={(value) => {
				if (allPosts.find(({ post_title }) => post_title === value)) {
					return
				}

				fetchPosts(value)
			}}
		/>
	)
}

export default PostIdPicker
