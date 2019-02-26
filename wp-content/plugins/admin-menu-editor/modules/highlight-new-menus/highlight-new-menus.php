<?php

class ameMenuHighlighterWrapper extends ameModule {
	public function __construct($menuEditor) {
		parent::__construct($menuEditor);

		if ( is_admin() ) {
			require dirname(__FILE__) . '/wsNewMenuHighlighter.php';
			new wsNewMenuHighlighter();
		}
	}
}