<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Integrations;

abstract class Integration {

	/**
	 * @var \Amazon_S3_And_CloudFront_Pro
	 */
	protected $as3cf;

	/**
	 * Integration constructor.
	 *
	 * @param \Amazon_S3_And_CloudFront_Pro $as3cf
	 */
	public function __construct( $as3cf ) {
		$this->as3cf = $as3cf;
	}

	/**
	 * Is installed?
	 *
	 * @return bool
	 */
	abstract public function is_installed();

	/**
	 * Init integration.
	 */
	abstract public function init();

}