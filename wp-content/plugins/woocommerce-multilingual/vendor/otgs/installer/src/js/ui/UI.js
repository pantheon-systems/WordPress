import '../../scss/ui/styles.scss';
import Switcher from './Switcher';

class UI {
	constructor (element) {
		const checkBoxes = element.querySelectorAll('input[type="checkbox"]');

		if(checkBoxes) {
			Array.from(checkBoxes).map(checkBox => new Switcher(checkBox));
		}
	}
}

export default UI;