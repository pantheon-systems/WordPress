<?php

class WCML_Currencies_Dropdown_UI {

	private $template_loader;
	private $template;

	function __construct( WPML_Twig_Template_Loader $template_loader ) {
		$this->template_loader = $template_loader;
	}

	public function get( $active_currencies, $selected_currency ) {
		$model = array(
			'active_currencies' => $active_currencies,
			'selected_currency' => $selected_currency,
		);

		return $this->get_template()->show( $model, 'currencies-dropdown.twig' );
	}

	private function get_template() {
		if ( null === $this->template ) {
			$this->template = $this->template_loader->get_template();
		}

		return $this->template;
	}

}