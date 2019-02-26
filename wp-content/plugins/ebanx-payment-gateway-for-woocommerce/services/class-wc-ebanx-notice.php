<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Notice
 */
class WC_EBANX_Notice {

	/**
	 * The message of the notice
	 *
	 * @var string
	 */
	private $message;

	/**
	 * The type of the notice
	 *
	 * @var string
	 */
	private $type = 'info';

	/**
	 * An array of valid types
	 *
	 * @var array
	 */
	private $allowed_types = array(
		'error',
		'warning',
		'success',
		'info',
	);

	/**
	 * Determines if the notice is dismissible
	 *
	 * @var boolean
	 */
	private $is_dismissible = true;

	/**
	 * View's file name
	 *
	 * @var string
	 */
	private $view;

	/**
	 * Constructor
	 */
	public function __construct() {
		$args = func_get_args();
		switch ( count( $args ) ) {
			case 3:
				$this->is_dismissible = $args[2];
				// FALLTHROUGH.
			case 2:
				$this->with_type( $args[1] );
				// FALLTHROUGH.
			case 1:
				$this->message = $args[0];
				break;
		}
	}

	/**
	 * If using a view file instead of a message, it sets the view file name
	 *
	 * @param  string $view
	 * @return WC_EBANX_Notices_Notice
	 */
	public function with_view( $view ) {
		$this->view = $view;
		return $this;
	}

	/**
	 * Sets the message of the notice
	 *
	 * @param  string $message
	 * @return WC_EBANX_Notices_Notice
	 */
	public function with_message( $message ) {
		$this->message = $message;
		return $this;
	}

	/**
	 * Sets the type of the notice
	 *
	 * @param  string $type
	 *
	 * @throws InvalidArgumentException When Unknown notice type received.
	 *
	 * @return WC_EBANX_Notices_Notice
	 */
	public function with_type( $type ) {
		if ( ! in_array( $type, $this->allowed_types ) ) {
			throw new InvalidArgumentException( 'Unknown notice type' );
		}
		$this->type = $type;
		return $this;
	}

	/**
	 * Makes the notice dismissible
	 *
	 * @return WC_EBANX_Notices_Notice
	 */
	public function dismissible() {
		$this->is_dismissible = true;
		return $this;
	}

	/**
	 * Makes the notice persistent
	 *
	 * @return WC_EBANX_Notices_Notice
	 */
	public function persistent() {
		$this->is_dismissible = false;
		return $this;
	}

	/**
	 * Enqueues the notice to the WordPress hook
	 *
	 * @throws Exception When no message is specified.
	 *
	 * @return WC_EBANX_Notices_Notice
	 */
	public function enqueue() {
		if ( isset( $this->view ) ) {
			$view = $this->view;
			add_action(
				'admin_notices', function () use ( $view ) {
					include WC_EBANX_TEMPLATES_DIR . 'views/html-notice-' . $view . '.php';
				}
			);
			$this->view = null;
			return $this;
		}
		if ( is_null( $this->message ) ) {
			throw new Exception( 'You need to specify a message' );
		}
		$type           = $this->type;
		$message        = $this->message;
		$is_dismissible = $this->is_dismissible;
		add_action(
			'admin_notices', function () use ( $type, $message, $is_dismissible ) {
				$classes = "notice notice-{$type}";
				if ( $is_dismissible ) {
					$classes .= ' is-dismissible';
				}
				$notice = "<div class='$classes'><p>{$message}</p></div>";
				echo $notice; // phpcs:ignore WordPress.XSS.EscapeOutput
			}
		);
		return $this;
	}

	/**
	 * Prints the notice when using hook won't work
	 *
	 * @throws Exception When no message is specified.
	 *
	 * @return WC_EBANX_Notices_Notice
	 */
	public function display() {
		if ( isset( $this->view ) ) {
			$view = $this->view;
			include WC_EBANX_TEMPLATES_DIR . 'views/html-notice-' . $view . '.php';
			$this->view = null;
			return $this;
		}
		if ( is_null( $this->message ) ) {
			throw new Exception( 'You need to specify a message' );
		}
		$type           = $this->type;
		$message        = $this->message;
		$is_dismissible = $this->is_dismissible;
		$classes        = "notice notice-{$type}";
		if ( $is_dismissible ) {
			$classes .= ' is-dismissible';
		}
		$notice = "<div class='$classes'><p>{$message}</p></div>";
		echo $notice; // phpcs:ignore WordPress.XSS.EscapeOutput
		return $this;
	}
}
