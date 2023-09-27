import { maybeApplyWordfenceCaptcha } from './captcha'

export const formPreSubmitHook = (form) =>
	new Promise((res) => {
		if (maybeApplyWordfenceCaptcha(res, form)) {
			return
		}

		res()
	})
