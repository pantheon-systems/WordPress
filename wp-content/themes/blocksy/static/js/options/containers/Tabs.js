import { createElement, Fragment, Component } from '@wordpress/element'
import OptionsPanel from '../OptionsPanel'
import classnames from 'classnames'
import { normalizeCondition, matchValuesWithCondition } from 'match-conditions'

export default class Tabs extends Component {
	state = {
		currentTab: 0
	}

	render() {
		const filteredTabs = this.props.renderingChunk.filter(
			singleTab =>
				!singleTab.condition ||
				matchValuesWithCondition(
					normalizeCondition(singleTab.condition),
					this.props.value
				)
		)

		const currentTab = filteredTabs[this.state.currentTab]

		return (
			<div className="ct-tabs">
				<ul>
					{filteredTabs
						.map((singleTab, index) => ({ singleTab, index }))
						.map(({ singleTab, index }) => (
							<li
								key={singleTab.id}
								onClick={() =>
									this.setState({ currentTab: index })
								}
								className={classnames({
									active: index === this.state.currentTab
								})}>
								{singleTab.title
									? singleTab.title
									: singleTab.id}
							</li>
						))}
				</ul>

				<div className="ct-current-tab">
					<OptionsPanel
						purpose={this.props.purpose}
						key={currentTab.id}
						onChange={(key, val) => this.props.onChange(key, val)}
						options={currentTab.options}
						value={this.props.value}
					/>
				</div>
			</div>
		)
	}
}

/*
const Condition = ({ renderingChunk, value, onChange }) =>
	renderingChunk.map(
		conditionOption =>
			matchValuesWithCondition(
				normalizeCondition(conditionOption.condition),
				value
			) ? (
				<OptionsPanel
					key={conditionOption.id}
					onChange={val => onChange({ ...value, ...val })}
					options={conditionOption.options}
					value={value}
				/>
			) : (
				[]
			)
	)

export default Condition

*/
