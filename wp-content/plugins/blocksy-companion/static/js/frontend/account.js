import ctEvents from 'ct-events'
import { formPreSubmitHook } from './account/hooks'
import { resetCaptchaFor, reCreateCaptchaFor } from './account/captcha'
import { mountPasswordStrength } from './account/password-strength'

const maybeAddLoadingState = (form) => {
	const maybeButton = form.querySelector('[name*="submit"]')

	if (!maybeButton) {
		return
	}

	maybeButton.classList.add('ct-loading')
}

const maybeCleanupLoadingState = (form) => {
	const maybeButton = form.querySelector('[name*="submit"]')

	if (!maybeButton) {
		return
	}

	maybeButton.classList.remove('ct-loading')
}

export const activateScreen = (
	el,
	{
		// login | register | forgot
		screen = 'login',
	}
) => {
	if (!el.querySelector(`.ct-${screen}-form`)) {
		screen = 'login'
	}

	if (el.querySelector('ul') && el.querySelector(`ul .ct-${screen}`)) {
		el.querySelector('ul .active').classList.remove('active')
		el.querySelector(`ul .ct-${screen}`).classList.add('active')
	}

	el.querySelector('[class*="-form"].active').classList.remove('active')
	el.querySelector(`.ct-${screen}-form`).classList.add('active')

	if (el.querySelector(`.ct-${screen}-form form`)) {
		el.querySelector(`.ct-${screen}-form form`).reset()
	}

	el.querySelector('.ct-account-form').classList.remove('ct-error')

	let maybeMessageContainer = el
		.querySelector(`.ct-${screen}-form`)
		.querySelector('.ct-form-notification')

	if (maybeMessageContainer) {
		maybeMessageContainer.remove()
	}

	let maybeErrorContainer = el
		.querySelector(`.ct-${screen}-form`)
		.querySelector('.ct-form-notification-error')

	if (maybeErrorContainer) {
		maybeErrorContainer.remove()
	}

	reCreateCaptchaFor(el)
}

let actuallyInsertError = (container, errorHtml) => {
	let maybeErrorContainer = container.querySelector(
		'.ct-form-notification-error'
	)

	if (maybeErrorContainer) {
		maybeErrorContainer.remove()
	}

	container.closest('.ct-account-form').classList.remove('ct-error')

	if (errorHtml) {
		container.insertAdjacentHTML(
			'afterbegin',
			`<div class="ct-form-notification-error">${errorHtml}</div>`
		)

		requestAnimationFrame(() => {
			container.closest('.ct-account-form').classList.add('ct-error')
		})
	}
}

const maybeAddErrors = (container, html) => {
	let parser = new DOMParser()
	let doc = parser.parseFromString(html, 'text/html')

	let maybeLoginError = doc.querySelector('#login_error')

	let errorHtml = ''

	if (maybeLoginError) {
		errorHtml = maybeLoginError.innerHTML
	}

	actuallyInsertError(container, errorHtml)

	return {
		hasError: !!maybeLoginError,
		doc,
	}
}

const actuallyInsertMessage = (container, html) => {
	let maybeMessageContainer = container.querySelector('.ct-form-notification')

	if (maybeMessageContainer) {
		maybeMessageContainer.remove()
	}

	container.closest('.ct-account-form').classList.remove('ct-error')

	if (html) {
		container.insertAdjacentHTML(
			'afterbegin',
			`<div class="ct-form-notification">${html}</div>`
		)
	}
}

const maybeAddMessage = (container, html) => {
	let parser = new DOMParser()
	let doc = parser.parseFromString(html, 'text/html')

	let maybeMessage = doc.querySelector('.message')

	let messageHtml = ''

	if (maybeMessage) {
		messageHtml = maybeMessage.innerHTML
	}

	actuallyInsertMessage(container, messageHtml)

	return { doc }
}

export const handleAccountModal = (el) => {
	if (!el) {
		return
	}

	if (el.hasListeners) {
		return
	}

	el.hasListeners = true

	let maybeLogin = el.querySelector('[name="loginform"]')
	let maybeRegister = el.querySelector('[name="registerform"]')
	let maybeLostPassword = el.querySelector('[name="lostpasswordform"]')

	el.addEventListener(
		'click',
		(e) => {
			if (e.target.href && e.target.href.indexOf('lostpassword') > -1) {
				activateScreen(el, { screen: 'forgot-password' })
				e.preventDefault()
			}

			if (e.target.href && e.target.classList.contains('showlogin')) {
				activateScreen(el, { screen: 'login' })
				e.preventDefault()
			}

			if (
				e.target.href &&
				(e.target.href.indexOf('wp-login') > -1 ||
					(maybeLogin && e.target.href === maybeLogin.action)) &&
				e.target.href.indexOf('lostpassword') === -1
			) {
				activateScreen(el, { screen: 'login' })
				e.preventDefault()
			}
		},
		true
	)
	;[...el.querySelectorAll('.show-password-input')].map((eye) => {
		eye.addEventListener('click', (e) => {
			eye.previousElementSibling.type =
				eye.previousElementSibling.type === 'password'
					? 'text'
					: 'password'
		})
	})

	if (maybeLogin) {
		maybeLogin.addEventListener('submit', (e) => {
			e.preventDefault()

			if (window.ct_customizer_localizations) {
				return
			}

			maybeAddLoadingState(maybeLogin)

			let body = new FormData(maybeLogin)

			// let url = maybeLogin.action

			let url = `${ct_localizations.ajax_url}?action=blc_implement_user_login`

			if (window.WFLSVars && !maybeLogin.loginProceed) {
				body.append('action', 'wordfence_ls_authenticate')
				url = WFLSVars.ajaxurl

				formPreSubmitHook(maybeLogin).then(() => {
					fetch(url, {
						method: maybeLogin.method,
						body,
					})
						.then((response) => response.json())
						.then((res) => {
							maybeCleanupLoadingState(maybeLogin)
							let hasError = !!res.error

							const container =
								maybeLogin.closest('.ct-login-form')
							const form = maybeLogin
								.closest('.ct-login-form')
								.querySelector('form')

							if (hasError) {
								actuallyInsertError(container, res.error)
							}

							if (res.message) {
								actuallyInsertMessage(form, res.message)
							}

							if (res.login) {
								if (res.jwt) {
									if (!jQuery('#wfls-token').length) {
										var overlay = jQuery(
											'<div id="wfls-prompt-overlay-blocksy"></div>'
										)

										var wrapper = jQuery(
											'<div id="wfls-prompt-wrapper"></div>'
										)
										var label = jQuery(
											'<label for="wfls-token">2FA Code <a href="javascript:void(0)" class="wfls-2fa-code-help wfls-tooltip-trigger" title="The 2FA Code can be found within the authenticator app you used when first activating two-factor authentication. You may also use one of your recovery codes."><i class="dashicons dashicons-editor-help"></i></a></label>'
										)
										var field = jQuery(
											'<input type="text" name="wfls-token" id="wfls-token" aria-describedby="wfls-token-error" class="input" value="" size="6" autocomplete="off"/>'
										)
										var remember = jQuery(
											'<label for="wfls-remember-device"><input name="wfls-remember-device" type="checkbox" id="wfls-remember-device" class="ct-checkbox" value="1" /> Remember for 30 days</label>'
										)

										wrapper.append(label)
										wrapper.append(field)

										if (parseInt(WFLSVars.allowremember)) {
											wrapper.append(remember)
										}

										overlay.append(wrapper)

										jQuery(form).prepend(overlay)

										new jQuery.Zebra_Tooltips(
											jQuery('.wfls-tooltip-trigger')
										)
									}

									var jwtField = jQuery('#wfls-token-jwt')

									if (!jwtField.length) {
										jwtField = jQuery(
											'<input type="hidden" name="wfls-token-jwt" id="wfls-token-jwt" value=""/>'
										)

										jQuery(
											'#wfls-prompt-overlay-blocksy'
										).append(jwtField)
									}

									jQuery('#wfls-token-jwt').val(res.jwt)
								} else {
									fetch(
										`${ct_localizations.ajax_url}?action=blc_implement_user_login`,
										{
											method: maybeLogin.method,
											body: new FormData(maybeLogin),
										}
									)
										.then((response) => response.text())
										.then((html) => {
											location = maybeLogin.querySelector(
												'[name="redirect_to"]'
											).value
										})
								}
							}

							if (res.combined) {
								maybeLogin.loginProceed = true

								fetch(
									`${ct_localizations.ajax_url}?action=blc_implement_user_login`,
									{
										method: maybeLogin.method,
										body: new FormData(maybeLogin),
									}
								)
									.then((response) => response.text())
									.then((html) => {
										location = maybeLogin.querySelector(
											'[name="redirect_to"]'
										).value
									})
							}

							if (
								!hasError ||
								(hasError &&
									maybeLogin
										.closest('.ct-login-form')
										.querySelector(
											'.ct-form-notification-error'
										)
										.innerHTML.indexOf('Captcha') === -1)
							) {
								resetCaptchaFor(
									maybeLogin.closest('.ct-login-form')
								)
							}
						})
				})

				return
			}

			formPreSubmitHook(maybeLogin).then(() => {
				fetch(url, {
					method: maybeLogin.method,
					body,
				})
					.then((response) => response.text())
					.then((html) => {
						maybeCleanupLoadingState(maybeLogin)
						const { doc, hasError } = maybeAddErrors(
							maybeLogin.closest('.ct-login-form'),
							html
						)

						if (!hasError) {
							setTimeout(() => {
								location = maybeLogin.querySelector(
									'[name="redirect_to"]'
								).value
							}, 2000)
						}

						if (
							!hasError ||
							(hasError &&
								maybeLogin
									.closest('.ct-login-form')
									.querySelector(
										'.ct-form-notification-error'
									)
									.innerHTML.indexOf('Captcha') === -1)
						) {
							resetCaptchaFor(
								maybeLogin.closest('.ct-login-form')
							)
						}
					})
			})
		})
	}

	if (maybeRegister) {
		maybeRegister.addEventListener('submit', (e) => {
			e.preventDefault()

			if (window.ct_customizer_localizations) {
				return
			}

			maybeAddLoadingState(maybeRegister)

			formPreSubmitHook(maybeRegister).then(() =>
				fetch(
					// maybeRegister.action,
					`${ct_localizations.ajax_url}?action=blc_implement_user_registration`,

					{
						method: maybeRegister.method,
						body: new FormData(maybeRegister),
					}
				)
					.then((response) => response.text())
					.then((html) => {
						const { doc, hasError } = maybeAddErrors(
							maybeRegister.closest('.ct-register-form'),
							html
						)

						maybeCleanupLoadingState(maybeRegister)

						if (!hasError) {
							maybeAddMessage(
								maybeRegister.closest('.ct-register-form'),
								html
							)
						}

						ctEvents.trigger(
							`blocksy:account:register:${
								hasError ? 'error' : 'success'
							}`
						)

						if (!hasError) {
							if (
								maybeRegister.querySelector(
									'[name="redirect_to"]'
								) &&
								maybeRegister.querySelector(
									'[name="role"][value="seller"]:checked'
								)
							) {
								location = maybeRegister.querySelector(
									'[name="redirect_to"]'
								).value
							}
						}

						if (
							!hasError ||
							(hasError &&
								maybeRegister
									.closest('.ct-register-form')
									.querySelector(
										'.ct-form-notification-error'
									)
									.innerHTML.indexOf('Captcha') === -1)
						) {
							resetCaptchaFor(
								maybeRegister.closest('.ct-register-form')
							)
						}
					})
			)
		})

		let maybePassField = maybeRegister.querySelector('#user_pass_register')

		if (maybePassField) {
			mountPasswordStrength(maybePassField)
		}
	}

	if (maybeLostPassword) {
		maybeLostPassword.addEventListener('submit', (e) => {
			e.preventDefault()

			if (window.ct_customizer_localizations) {
				return
			}

			maybeAddLoadingState(maybeLostPassword)

			fetch(
				// maybeLostPassword.action,
				`${ct_localizations.ajax_url}?action=blc_implement_user_lostpassword`,

				{
					method: maybeLostPassword.method,
					body: new FormData(maybeLostPassword),
				}
			)
				.then((response) => response.text())
				.then((html) => {
					const { doc, hasError } = maybeAddErrors(
						maybeLostPassword.closest('.ct-forgot-password-form'),
						html
					)

					maybeCleanupLoadingState(maybeLostPassword)

					if (!hasError) {
						maybeAddMessage(
							maybeLostPassword.closest(
								'.ct-forgot-password-form'
							),
							html
						)
					}

					if (
						!hasError ||
						(hasError &&
							maybeLostPassword
								.closest('.ct-forgot-password-form')
								.querySelector('.ct-form-notification-error')
								.innerHTML.indexOf('Captcha') === -1)
					) {
						resetCaptchaFor(
							maybeLostPassword.closest(
								'.ct-forgot-password-form'
							)
						)
					}
				})
		})
	}

	;['login', 'register', 'forgot-password'].map((screen) => {
		Array.from(el.querySelectorAll(`.ct-${screen}`)).map((itemEl) => {
			itemEl.addEventListener('click', (e) => {
				e.preventDefault()
				activateScreen(el, { screen })
			})

			itemEl.addEventListener('keyup', (e) => {
				if (e.keyCode !== 13) {
					return
				}

				e.preventDefault()
				activateScreen(el, { screen })
			})
		})
	})
}
