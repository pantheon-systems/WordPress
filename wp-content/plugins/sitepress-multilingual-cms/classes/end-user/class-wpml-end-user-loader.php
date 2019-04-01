<?php

class WPML_End_User_Loader implements IWPML_Action, Countable {
	/** @var  IWPML_Action[] */
	private $loaders;

	/**
	 * @param IWPML_Action[] $loaders
	 */
	public function __construct( array $loaders ) {
		$this->loaders = array_filter( $loaders, array( $this, 'is_loader' ) );
	}


	public function add_hooks() {
		foreach ( $this->loaders as $loader ) {
			$loader->add_hooks();
		}
	}

	public function count() {
		return count( $this->loaders );
	}

	/**
	 * @param mixed $class
	 *
	 * @return bool
	 */
	private function is_loader( $class ) {
		return $class instanceof IWPML_Action;
	}
}
