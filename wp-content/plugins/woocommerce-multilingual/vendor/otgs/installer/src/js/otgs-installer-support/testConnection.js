/*globals ajaxurl*/

class testConnection {

	constructor( element ) {
		this.container = element.querySelector('.otgs-installer-support-test-connection');
	}

	addEvents() {
		this.container.querySelector('.check-again').addEventListener('click', () => this.checkConnection());
	}

	checkConnection() {
		const cssClasses = {
			successClass: 'dashicons-yes',
			failClass: 'dashicons-no-alt',
			iconClass: 'dashicons',
			spinnerClass: 'spinner',
			activeSpinnerClass: 'is-active'
		};

		this.container.querySelectorAll( '.endpoint' ).forEach((el) => {

			const status = el.querySelector('.status'),
				nonce = this.container.querySelector('#otgs_installer_test_connection').value;

			this.resetStatus(status, cssClasses);

			fetch(ajaxurl, {
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
				},
				credentials: 'same-origin',
				method: "POST",
				body: `nonce=${nonce}&action=otgs_installer_test_connection&repository=${el.getAttribute('data-repository')}&type=${el.getAttribute('data-type')}`
			})
				.then(response => {
					response.json()
						.then(res => {
							if (res.success) {
								this.setSuccessStatus(status, cssClasses);
							} else {
								this.setFailStatus(status, cssClasses);
							}
						});
				});
		});
	}

	resetStatus(status, cssClasses) {
		status.classList.remove(cssClasses.successClass, cssClasses.failClass, cssClasses.iconClass);
		status.classList.add(cssClasses.spinnerClass, cssClasses.activeSpinnerClass);
	}

	setSuccessStatus(status, cssClasses) {
		status.classList.add(cssClasses.successClass, cssClasses.iconClass);
		status.classList.remove(cssClasses.failClass, cssClasses.spinnerClass, cssClasses.activeSpinnerClass);
	}

	setFailStatus(status, cssClasses) {
		status.classList.remove(cssClasses.successClass, cssClasses.spinnerClass, cssClasses.activeSpinnerClass);
		status.classList.add(cssClasses.failClass, cssClasses.iconClass);
	}
}

export default testConnection;