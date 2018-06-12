<?php
/**
 * Abstract Sandbox Task class.
 *
 * @package Wsal
 */
abstract class WSAL_AbstractSandboxTask {

	/**
	 * Method: Constructor.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		// Remove time limit and clear output buffers.
		set_time_limit( 0 );
		ob_implicit_flush( true );
		while ( ob_get_level() ) {
			ob_end_flush();
		}

		// Set up shutdown handler.
		register_shutdown_function( array( $this, 'Shutdown' ) );

		// Run event sequence.
		$this->Header();
		try {
			$this->Execute();
		} catch ( Exception $ex ) {
			$this->Message( get_class( $ex ) . ' [' . basename( $ex->getFile() ) . ':' . $ex->getLine() . ']: ' . $ex->getMessage() );
			$this->Message( $ex->getTraceAsString(), true );
		}
		$this->Footer();

		// Shutdown.
		die();
	}

	/**
	 * Header.
	 */
	protected function Header() {
		echo '<!DOCTYPE html><html><body style="margin: 0; padding: 8px; font: 12px Arial; color: #333;">';
		echo '<div style="position: fixed; top: 0; left: 0; right: 0; padding: 8px; background: #F0F0F0;">';
		echo '  <div id="bar" style=" border-top: 2px solid #0AE; top: 20px; height: 0; width: 0%;"> </div>';
		echo '  <span id="msg"></span> <span id="prg"></span>';
		echo '</div>';
		echo '<div id="msgs" style="font-family: Consolas; margin-top: 30px; white-space: pre;"></div>';
		echo '<script>';
		echo '  var bar = document.getElementById("bar");';
		echo '  var msg = document.getElementById("msg");';
		echo '  var prg = document.getElementById("prg");';
		echo '  var msgs = document.getElementById("msgs");';
		echo '</script>';
		flush();
	}

	/**
	 * Footer.
	 */
	protected function Footer() {
		echo '<div style="display: none;">';
		flush();
	}

	/**
	 * Method: Execute.
	 */
	protected abstract function Execute();

	/**
	 * Method: Shutdown.
	 */
	public function Shutdown() {
		echo '</div></body></html>';
		flush();
	}

	/**
	 * Method: Show progress.
	 *
	 * @param mix $percent - Progress percentage.
	 */
	protected function Progress( $percent ) {
		echo '<script>bar.style.width=prg.innerHTML="' . number_format( $percent, 2 ) . '%";</script>';
		flush();
	}

	/**
	 * Method: Message.
	 *
	 * @param string $message - Message.
	 * @param bool   $sticky - True if sticky.
	 */
	protected function Message( $message, $sticky = false ) {
		if ( $sticky ) {
			echo '<script>msgs.appendChild(document.createTextNode(' . json_encode( $message . PHP_EOL ) . ')); window.scroll(0, document.body.scrollHeight);</script>';
		} else {
			echo '<script>msg.innerHTML=' . json_encode( $message ) . ';</script>';
		}
		flush();
	}
}
