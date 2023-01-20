const getRelevantInputs = (formEl) => {
	var inputs = jQuery()

	;[...formEl.querySelectorAll('input')].map((el) => {
		inputs = inputs.add(el)
	})

	return inputs
}

var wfls_init_captcha = function (actionCallback, formEl) {
	let log = getRelevantInputs(formEl)

	if (typeof grecaptcha === 'object') {
		grecaptcha.ready(function () {
			grecaptcha
				.execute(WFLSVars.recaptchasitekey, { action: 'login' })
				.then(function (token) {
					var tokenField = jQuery('#wfls-captcha-token')
					if (tokenField.length) {
						tokenField.val(token)
					} else {
						if (log.length) {
							tokenField = jQuery(
								'<input type="hidden" name="wfls-captcha-token" id="wfls-captcha-token" />'
							)
							tokenField.val(token)
							log.parent().append(tokenField)
						}
					}

					typeof actionCallback === 'function' && actionCallback(true)
				})
		})
	} else {
		var tokenField = jQuery('#wfls-captcha-token')

		if (tokenField.length) {
			tokenField.val('grecaptcha-missing')
		} else {
			if (log.length) {
				tokenField = jQuery(
					'<input type="hidden" name="wfls-captcha-token" id="wfls-captcha-token" />'
				)
				tokenField.val('grecaptcha-missing')
				log.parent().append(tokenField)
			}
		}

		typeof actionCallback === 'function' && actionCallback(true)
	}
}

export const maybeApplyWordfenceCaptcha = (cb, form) => {
	if (window.WFLSVars && parseInt(WFLSVars.useCAPTCHA)) {
		wfls_init_captcha(() => cb(), form)
		return true
	}

	return false
}

export const resetCaptchaFor = (container) => {
	;[...container.querySelectorAll('.g-recaptcha, .anr_captcha_field')].map(
		(el) => {
			if (el.classList.contains('anr_captcha_field')) {
				grecaptcha.reset(
					parseFloat(
						el.firstElementChild.id.replace(
							'anr_captcha_field_',
							''
						)
					) - 1
				)
			} else {
				grecaptcha.reset(el.gID)
			}
		}
	)
}

export const reCreateCaptchaFor = (el) => {
	;[...el.querySelectorAll('.g-recaptcha, .anr_captcha_field')].map((el) => {
		if (el.gID) {
			return
		}

		el.innerHTML = ''
		el.gID = grecaptcha.render(el)
	})
}
