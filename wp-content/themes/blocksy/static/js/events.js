/**
 * Probably split string into general purpose object representation for
 * event names and listeners. This function leaves objects un-modified.
 *
 * @param topicStringOrObject {String | Object}
 * @param listener {Function | false}
 *
 * @returns {Object} {
 *    eventname: listener,
 *    otherevent: listener
 * }
 */
const splitTopicStringOrObject = (topicStringOrObject, listener) =>
	typeof topicStringOrObject !== 'string'
		? topicStringOrObject
		: topicStringOrObject
				.replace(/\s\s+/g, ' ')
				.trim()
				.split(' ')
				.reduce(
					(allEvents, event) => ({
						...allEvents,
						[event]: listener,
					}),

					{}
				)

class EventsManager {
	_events = {}

	on(topicStringOrObject, listener) {
		const eventsAndListeners = splitTopicStringOrObject(
			topicStringOrObject,
			listener
		)

		Object.keys(eventsAndListeners).map(
			(eventName) =>
				(this._events = {
					...this._events,
					[eventName]: [
						...(this._events[eventName] || []),
						eventsAndListeners[eventName],
					],
				})
		)

		return this
	}

	/**
	 * In order to remove one single listener you should give as an argument
	 * the same callback function. If you want to remove *all* listeners from
	 * a particular event you should not pass the second argument.
	 *
	 * @param topicStringOrObject {String | Object}
	 * @param listener {Function | false}
	 */
	off(topicStringOrObject, listener) {
		const eventsAndListeners = splitTopicStringOrObject(
			topicStringOrObject,
			listener
		)

		Object.keys(eventsAndListeners).map((eventName) => {
			if (this._events[eventName]) {
				if (eventsAndListeners[eventName]) {
					this._events[eventName].splice(
						this._events[eventName].indexOf(listener) >>> 0,
						1
					)
				} else {
					this._events[eventName] = []
				}
			}
		})

		return this
	}

	/**
	 * Trigger an event. In case you provide multiple events via space-separated
	 * string or an object of events it will execute listeners for each event
	 * separatedly. You can use the "all" event to trigger all events.
	 *
	 * @param topicStringOrObject {String | Object}
	 * @param data {Object}
	 */
	trigger(eventName, data) {
		const events = splitTopicStringOrObject(eventName)

		const dispatchSingleEvent = (listenerDescriptor) =>
			listenerDescriptor && listenerDescriptor.call(window, data)

		Object.keys(events).map((eventName) => {
			try {
				;(this._events[eventName] || []).map(dispatchSingleEvent)
				;(this._events['all'] || []).map(dispatchSingleEvent)
			} catch (e) {
				console.log(
					'%c [Events] Exception raised.',
					'color: red; font-weight: bold;'
				)

				if (typeof console !== 'undefined') {
					console.error(e)
				} else {
					throw e
				}
			}
		})

		return this
	}
}

const events = new EventsManager()

window.ctEvents = events

export default events
