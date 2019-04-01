<?php

class WPML_Action_Filter_Loader {

	/** @var  array $defered_actions */
	private $defered_actions = array();

	/** @var  WPML_AJAX_Action_Validation $ajax_action_validation */
	private $ajax_action_validation;

	/**
	 * @param string[] $loaders
	 */
	public function load( $loaders ) {
		foreach ( $loaders as $loader ) {
			$implementations = class_implements( $loader );

			$backend  = in_array( 'IWPML_Backend_Action_Loader', $implementations, true );
			$frontend = in_array( 'IWPML_Frontend_Action_Loader', $implementations, true );
			$ajax     = in_array( 'IWPML_AJAX_Action_Loader', $implementations, true );
			$rest     = in_array( 'IWPML_REST_Action_Loader', $implementations, true );

			if ( $backend && $frontend ) {
				$this->load_factory( $loader );
			} elseif ( $backend && is_admin() ) {
				$this->load_factory( $loader );
			} elseif ( $frontend && ! is_admin() ) {
				$this->load_factory( $loader );
			} elseif ( $ajax && wpml_is_ajax() ) {
				$this->load_factory( $loader );
			}
			if ( $rest ) {
				$this->load_factory( $loader );
			}
		}
	}

	/**
	 * @param string $loader
	 */
	private function load_factory( $loader ) {
		/** @var IWPML_Action_Loader_Factory $factory */
		$factory = new $loader();

		if( $factory instanceof WPML_AJAX_Base_Factory ) {
			/** @var WPML_AJAX_Base_Factory $factory */
			$factory->set_ajax_action_validation( $this->get_ajax_action_validation() );
		}

		if ( $factory instanceof IWPML_Deferred_Action_Loader ) {
			$this->add_deferred_action( $factory );
		} else {
			$this->run_factory( $factory );
		}
	}

	/**
	 * @param IWPML_Deferred_Action_Loader $factory
	 */
	private function add_deferred_action( IWPML_Deferred_Action_Loader $factory ) {
		$action = $factory->get_load_action();
		if ( ! isset( $this->defered_actions[ $action ] ) ) {
			$this->defered_actions[ $action ] = array();
			add_action( $action, array( $this, 'deferred_loader' ) );
		}
		$this->defered_actions[ $action ][] = $factory;
	}

	public function deferred_loader() {
		$action = current_action();
		foreach ( $this->defered_actions[ $action ] as $factory ) {
			/** @var IWPML_Deferred_Action_Loader $factory */
			$this->run_factory( $factory );
		}
	}

	/**
	 * @return WPML_AJAX_Action_Validation
	 */
	private function get_ajax_action_validation() {
		if ( ! $this->ajax_action_validation ) {
			$this->ajax_action_validation = new WPML_AJAX_Action_Validation();
		}

		return $this->ajax_action_validation;
	}

	private function run_factory( IWPML_Action_Loader_Factory $factory ) {
		$load_handlers = $factory->create();

		if ( $load_handlers ) {
			if ( ! is_array( $load_handlers ) ) {
				$load_handlers = array( $load_handlers );
			}
			foreach ( $load_handlers as $load_handler ) {
				$load_handler->add_hooks();
			}
		}
	}
}
