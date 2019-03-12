import UI from './UI';

window.addEventListener('DOMContentLoaded', () => {

	const otgsUIElements = document.querySelectorAll('.otgs-ui');

	if (otgsUIElements) {
		Array.from(otgsUIElements).map(otgsUI => new UI(otgsUI));
	}
});