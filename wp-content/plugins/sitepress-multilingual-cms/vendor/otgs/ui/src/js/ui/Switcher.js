class Switcher {
	constructor (element) {
		this.element           = element;
		this.checkBoxContainer = this.element.parentElement;
		this.heading           = this.checkBoxContainer.getElementsByClassName('heading');
		this.label             = this.checkBoxContainer.getElementsByTagName('label').item(0);

		this.init();
	}

	init() {
		this.createElements();
		this.addClasses();
		this.buildTree();
		this.reorganizeElements();
	}

	reorganizeElements() {
		if (this.heading.length) {
			this.heading.item(this.heading.length - 1).parentNode
				.insertBefore(this.toggleGroup, this.heading.item(this.heading.length - 1).nextSibling);
		} else {
			this.checkBoxContainer.insertBefore(this.toggleGroup, this.checkBoxContainer.firstChild);
		}
	}

	buildTree() {
		this.toggleGroup.appendChild(this.element);
		this.toggleGroup.appendChild(this.label);

		this.switcherBorder.appendChild(this.switcherInner);
		this.switcherBorder.appendChild(this.switcherSwitch);

		this.switcherContainer.appendChild(this.switcherBorder);

		this.toggleGroup.appendChild(this.switcherContainer);

		this.checkBoxContainer.appendChild(this.toggleGroup);
	}

	createElements() {
		this.toggleGroup       = document.createElement('label');
		this.switcherContainer = document.createElement('span');
		this.switcherBorder    = document.createElement('span');
		this.switcherInner     = document.createElement('span');
		this.switcherSwitch    = document.createElement('span');
	}

	addClasses() {
		if (this.label) {
			this.label.classList.add('otgs-on-off-switch');
		}

		this.toggleGroup.classList.add('otgs-toggle-group');
		this.switcherContainer.classList.add('otgs-switch__onoff');
		this.switcherBorder.classList.add('otgs-switch__onoff-label');
		this.switcherInner.classList.add('otgs-switch__onoff-inner');
		this.switcherSwitch.classList.add('otgs-switch__onoff-switch');
	}
}

export default Switcher;