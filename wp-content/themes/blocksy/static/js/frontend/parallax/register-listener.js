import { Rellax } from './rellax'
import { onDocumentLoaded } from '../../helpers'

export let rel = new Rellax()

/**
 * TODO: maybe implement code splitting for parallax elements.
 * It will speed the up the process a lot.
 *
 * Maybe do that at the lib level.
 *
 * We can go about extracting the animate() function into a separated module.
 * This module will be shared among this and the rellax lib code.
 *
 * That way, we can defer execution of the rellax lib. But I guess the code
 * inside rellax.js is very coupled for doing that trick.
 */

export const mount = (elWithParallax) => {
	// Consider here storing the rellax instance onto the section DOM
	// element itself. And do that in a non-leaking fashion.
	//
	// section.rellaxInstance would leak memory
	if (
		elWithParallax.ctHasParallax &&
		elWithParallax.querySelector('figure .ct-image-container > img')
	) {
		return
		/*
		rel.removeEl({
			el: elWithParallax.querySelector(
				'figure .ct-image-container > img'
			),
		})
        */
	}

	if (
		elWithParallax.matches('[data-parallax]') &&
		!elWithParallax.dataset.parallax
	) {
		elWithParallax.removeAttribute('data-parallax')
		return
	}

	elWithParallax.ctHasParallax = true

	if (elWithParallax.querySelector('figure .ct-image-container > img')) {
		setTimeout(() => {
			rel.addEl({
				el: elWithParallax.querySelector(
					'figure .ct-image-container > img'
				),
				// +elWithParallax.dataset.parallaxSpeed,
				speed: -5,
				fitInsideContainer: elWithParallax,
				...(elWithParallax.dataset.parallax
					? { parallaxBehavior: elWithParallax.dataset.parallax }
					: {}),
			})
		}, 0)
	} else {
		rel.addEl({
			el: elWithParallax,
			speed: +elWithParallax.dataset.parallax,
			shouldSetHeightToIncrease: false,
		})
	}
}
