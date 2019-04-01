<?php
/**
 * Enables WooCommerce, via the the command line.
 *
 * @package WooCommerce\CLI
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * CLI class.
 */
class WC_CLI {
	/**
	 * Load required files and hooks to make the CLI work.
	 */
	public function __construct() {
		$this->includes();
		$this->hooks();
	}

	/**
	 * Load command files.
	 */
	private function includes() {
		require_once dirname( __FILE__ ) . '/cli/class-wc-cli-runner.php';
		require_once dirname( __FILE__ ) . '/cli/class-wc-cli-rest-command.php';
		require_once dirname( __FILE__ ) . '/cli/class-wc-cli-tool-command.php';
		require_once dirname( __FILE__ ) . '/cli/class-wc-cli-update-command.php';
	}

	/**
	 * Sets up and hooks WP CLI to our CLI code.
	 */
	private function hooks() {
		WP_CLI::add_hook( 'after_wp_load', 'WC_CLI_Runner::after_wp_load' );
		WP_CLI::add_hook( 'after_wp_load', 'WC_CLI_Tool_Command::register_commands' );
		WP_CLI::add_hook( 'after_wp_load', 'WC_CLI_Update_Command::register_commands' );
	}
}

new WC_CLI();
