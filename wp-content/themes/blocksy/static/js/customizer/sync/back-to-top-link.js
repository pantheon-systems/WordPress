import { responsiveClassesFor } from './helpers'
import ctEvents from 'ct-events'

const render = () => {
	const backTop = document.querySelector('.ct-back-to-top')

	ctEvents.trigger('ct:back-to-top:mount')

	responsiveClassesFor('back_top_visibility', backTop)

	backTop.dataset.shape = wp.customize('top_button_shape')()
	backTop.dataset.alignment = wp.customize('top_button_alignment')()
}

wp.customize('top_button_shape', (val) => {
	val.bind((to) => render())
})

wp.customize('top_button_alignment', (val) => {
	val.bind((to) => render())
})

wp.customize('back_top_visibility', (val) => {
	val.bind((to) => render())
})
