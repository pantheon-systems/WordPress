import $ from 'jquery'

export const mountPasswordStrength = (el) => {
	const includeMeter = function (wrapper, field) {
		var meter = $(wrapper).find('.woocommerce-password-strength')

		if ('' === $(field).val()) {
			meter.hide()
			$(document.body).trigger('wc-password-strength-hide')
		} else if (0 === meter.length) {
			$(field.nextElementSibling).after(
				'<div class="woocommerce-password-strength" aria-live="polite"></div>'
			)
			$(document.body).trigger('wc-password-strength-added')
		} else {
			meter.show()
			$(document.body).trigger('wc-password-strength-show')
		}
	}

	const checkPasswordStrength = function (wrapper, field) {
		let meter = $(wrapper).find('.woocommerce-password-strength')
		let hint = $(wrapper).find('.woocommerce-password-hint')
		let hint_html =
			'<small class="woocommerce-password-hint">' +
			wc_password_strength_meter_params.i18n_password_hint +
			'</small>'
		let strength = wp.passwordStrength.meter(
			$(field).val(),
			wp.passwordStrength.userInputDisallowedList()
		)
		let error = ''

		// Reset.
		meter.removeClass('short bad good strong')
		hint.remove()

		if (meter.is(':hidden')) {
			return strength
		}

		if (
			strength < wc_password_strength_meter_params.min_password_strength
		) {
			error =
				' - ' + wc_password_strength_meter_params.i18n_password_error
		}

		switch (strength) {
			case 0:
				meter.addClass('short').html(pwsL10n['short'] + error)
				meter.after(hint_html)
				break
			case 1:
				meter.addClass('bad').html(pwsL10n.bad + error)
				meter.after(hint_html)
				break
			case 2:
				meter.addClass('bad').html(pwsL10n.bad + error)
				meter.after(hint_html)
				break
			case 3:
				meter.addClass('good').html(pwsL10n.good + error)
				break
			case 4:
				meter.addClass('strong').html(pwsL10n.strong + error)
				break
			case 5:
				meter.addClass('short').html(pwsL10n.mismatch)
				break
		}

		return strength
	}

	const updateStrength = () => {
		var wrapper = el.closest('form')

		includeMeter(wrapper, el)
		let strength = checkPasswordStrength(wrapper, el)

		let submit = $(wrapper).find('button[name="wp-submit"]')

		if (
			el.value.length > 0 &&
			strength <
				wc_password_strength_meter_params.min_password_strength &&
			-1 !== strength
		) {
			submit.attr('disabled', 'disabled').addClass('disabled')
		} else {
			submit.prop('disabled', false).removeClass('disabled')
		}
	}

	el.addEventListener('keyup', updateStrength)
	el.addEventListener('change', updateStrength)
}
