<?php

/**
 * Class WPML_Twig_Template_Loader
 */
class WPML_Twig_Template_Loader {

	/**
	 * @var array
	 */
	private $paths;

	/**
	 * WPML_Twig_Template_Loader constructor.
	 *
	 * @param array $paths
	 */
	public function __construct( array $paths ) {
		$this->paths = $paths;
	}

	/**
	 * @return WPML_Twig_Template
	 */
	public function get_template() {
		$twig_loader      = new Twig_Loader_Filesystem( $this->paths );
		$environment_args = array();
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$environment_args['debug'] = true;
		}
		$twig = new Twig_Environment( $twig_loader, $environment_args );
		return new WPML_Twig_Template( $twig );
	}
}