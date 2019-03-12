class Switcher {
	constructor (element) {
		const checkBoxContainer = element.parentElement;
		const heading = checkBoxContainer.getElementsByClassName('heading');
		const label = checkBoxContainer.getElementsByTagName('label').item(0);

		if (label) {
			label.classList.add('otgs-on-off-switch');
		}

		const toggleGroup = document.createElement('label');
		toggleGroup.classList.add('otgs-toggle-group');
		toggleGroup.appendChild(element);
		toggleGroup.appendChild(label);

		const switcherContainer = document.createElement('span');
		switcherContainer.classList.add('otgs-switch__onoff');
		const switcherBorder = document.createElement('span');
		switcherBorder.classList.add('otgs-switch__onoff-label');
		const switcherInner = document.createElement('span');
		switcherInner.classList.add('otgs-switch__onoff-inner');
		const switcherSwitch = document.createElement('span');
		switcherSwitch.classList.add('otgs-switch__onoff-switch');

		switcherBorder.appendChild(switcherInner);
		switcherBorder.appendChild(switcherSwitch);

		switcherContainer.appendChild(switcherBorder);

		toggleGroup.appendChild(switcherContainer);

		checkBoxContainer.appendChild(toggleGroup);

		if (heading.length) {
			heading.item(heading.length - 1).parentNode
				.insertBefore(toggleGroup, heading.item(heading.length - 1).nextSibling);
		} else {
			checkBoxContainer.insertBefore(toggleGroup, checkBoxContainer.firstChild);
		}
	}

}

export default Switcher;