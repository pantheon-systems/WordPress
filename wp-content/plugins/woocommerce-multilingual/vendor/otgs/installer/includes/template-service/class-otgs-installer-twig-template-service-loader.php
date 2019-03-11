<?php

class OTGS_Installer_Twig_Template_Service_Loader {

	/**
	 * @var array
	 */
	private $paths;

	/**
	 * OTGS_Installer_Twig_Template_Service_Loader constructor.
	 *
	 * @param array $paths
	 */
	public function __construct( array $paths ) {
		$this->paths = $paths;
	}

	/**
	 * @return OTGS_Installer_Twig_Template_Service
	 */
	public function get_service() {
		if ( ! class_exists( 'Twig_Loader_Filesystem' ) ) {
			OTGS_Twig_Autoloader::register();
		}

		$twig_loader      = new Twig_Loader_Filesystem( $this->paths );
		$environment_args = array();
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$environment_args['debug'] = true;
		}
		$twig = new Twig_Environment( $twig_loader, $environment_args );

		return new OTGS_Installer_Twig_Template_Service( $twig );
	}
}