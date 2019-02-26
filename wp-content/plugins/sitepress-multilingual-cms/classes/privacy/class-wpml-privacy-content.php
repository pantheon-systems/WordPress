<?php

/**
 * @author OnTheGo Systems
 */
abstract class WPML_Privacy_Content implements IWPML_Action {

	public function add_hooks() {
		add_action( 'admin_init', array( $this, 'privacy_policy' ) );
	}

	public function privacy_policy() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		$policy_text_content = $this->get_privacy_policy();
		if ( $policy_text_content ) {
			if ( is_array( $policy_text_content ) ) {
				$policy_text_content = '<p>' . implode( '</p><p>', $policy_text_content ) . '</p>';
			}
			wp_add_privacy_policy_content( $this->get_plugin_name(), $policy_text_content );
		}
	}

	/**
	 * @return string
	 */
	abstract protected function get_plugin_name();

	/**
	 * @return string|array a single or an array of strings (plain text or HTML). Array items will be wrapped by a paragraph tag.
	 */
	abstract protected function get_privacy_policy();
}