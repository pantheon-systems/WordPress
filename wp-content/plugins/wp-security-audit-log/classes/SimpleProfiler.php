<?php
/**
 * @package Wsal
 */
class WSAL_SimpleProfiler {

	protected $_items = array();

	public function Start( $name ) {
		$item = new WSAL_SimpleProfiler_Item( $name );
		$this->_items[] = $item;
		return $item;
	}

	public function AsComment() {
		echo '<!-- ' . esc_html( PHP_EOL );
		foreach ( $this->_items as $item ) {
			echo '  ' . esc_html( $item . PHP_EOL );
		}
		echo '-->' . esc_html( PHP_EOL );
	}

	public function GetItems() {
		return $this->_items;
	}
}

class WSAL_SimpleProfiler_Item {

	public function __construct( $name ) {
		$this->name = $name;
		$this->t_bgn = microtime( true );
		$this->m_bgn = memory_get_usage();
	}

	public function Stop() {
		$this->t_end = microtime( true );
		$this->m_end = memory_get_usage();
	}

	public function __toString() {
		$t_diff = $this->t_end - $this->t_bgn;
		$m_diff = $this->m_end - $this->m_bgn;
		return number_format( $t_diff, 6 ) . 's '
		. str_pad( number_format( $m_diff, 0 ), 12, ' ', STR_PAD_LEFT ) . 'b '
		. $this->name;
	}
}
