<?php

class WPML_Upgrade_Chinese_Flags implements IWPML_Upgrade_Command {

	private $wpdb;

	/**
	 * WPML_Upgrade_Chinese_Flags constructor.
	 *
	 * @param array $args {
	 *                    'wpdb' => @type wpdb
	 *                    }
	 */
	public function __construct( array $args ) {
		$this->wpdb = $args['wpdb'];
	}

	public function run() {
		$codes = array( 'zh-hans', 'zh-hant' );

		$flags_query = 'SELECT id, lang_code, flag FROM ' . $this->wpdb->prefix . 'icl_flags WHERE lang_code IN (' . wpml_prepare_in( $codes ) . ')';
		$flags       = $this->wpdb->get_results( $flags_query );

		if ( $flags ) {
			foreach ( $flags as $flag ) {
				if ( $this->must_update( $flag ) ) {
					$this->wpdb->update( $this->wpdb->prefix . 'icl_flags',
						array(
							'flag' => 'zh.png'
						),
						array( 'id' => $flag->id ),
						array( '%s' ),
						array( '%d' )
					);
				}
			}
		}

		return true;
	}

	/**
	 * @param \stdClass $flag
	 *
	 * @return bool
	 */
	protected function must_update( $flag ) {
		return $flag->flag === $flag->lang_code . '.png';
	}

	public function run_admin() {
		return $this->run();
	}

	public function run_ajax() {
		return null;
	}

	public function run_frontend() {
		return null;
	}

	public function get_results() {
		return null;
	}
}
