import '../../scss/otgs-installer-support/styles.scss';
import testConnection from './testConnection.js';

document.addEventListener('DOMContentLoaded', function(){
	const testConnectionObj = new testConnection( document );
	testConnectionObj.addEvents();
});