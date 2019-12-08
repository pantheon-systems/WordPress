<?php
/**
 * Renders debugging information in the Debug Bar
 *
 * @package Solr_Power
 */

/**
 * Renders debugging information in the Debug Bar
 */
class SolrPower_Debug extends Debug_Bar_Panel {

	/**
	 * Initialize the panel
	 */
	function init() {
		$this->title( 'Solr' );
	}

	/**
	 * Pre-render the panel
	 */
	function prerender() {
		$log = SolrPower_Api::get_instance()->log;
		$this->set_visible( ! empty( $log ) );
	}

	/**
	 * Render the full panel
	 */
	function render() {

		echo '<h2>Solr Information</h2>';

		$log = SolrPower_Api::get_instance()->log;

		echo '<table>';
		foreach ( $log as $label => $value ) :
			echo '<tr>';
			echo '<td><strong>' . esc_html( $label ) . ':</strong></td>';
			if ( is_array( $value ) ) {
				echo '<td><ul>';
				foreach ( $value as $val ) {
					echo '<li>' . esc_html( $val ) . '</li>';
				}
				echo '</ul></td>';
			} else {
				echo '<td>' . esc_html( $value ) . '</td>';
			}
			echo '</tr>';
		endforeach;
		echo '</table>';
	}

}
