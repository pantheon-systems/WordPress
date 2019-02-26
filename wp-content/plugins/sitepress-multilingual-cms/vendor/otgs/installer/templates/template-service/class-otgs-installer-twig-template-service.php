<?php

class OTGS_Installer_Twig_Template_Service implements IOTGS_Installer_Template_Service {

	const FILE_EXTENSION = '.twig';

	private $twig;

	/**
	 * OTGS_Installer_Twig_Template_Service constructor.
	 *
	 * @param Twig_Environment $twig
	 */
	public function __construct( Twig_Environment $twig ) {
		$this->twig = $twig;
	}

	/**
	 * @param array $model
	 * @param string $template
	 *
	 * @return string
	 */
	public function show( $model, $template ) {
		return $this->twig->render( $template . self::FILE_EXTENSION, $model );
	}
}