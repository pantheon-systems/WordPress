<?php

class Blocksy_Walker_Page extends Walker_Page {
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if (
			isset( $args['item_spacing'] )
			&&
			'preserve' === $args['item_spacing']
		) {
			$t = "\t";
			$n = "\n";
		} else {
			$t = '';
			$n = '';
		}

		$indent  = str_repeat( $t, $depth );
		$output .= "{$n}{$indent}<ul class='sub-menu' role='menu'>{$n}";
	}

	public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
		parent::start_el(
			$output,
			$page,
			$depth,
			$args,
			$current_page
		);

		$output = str_replace(
			"</a><ul class='sub-menu' role='menu'>",
			"~</a>^<ul class='sub-menu' role='menu'>",
			$output
		);

		$output = str_replace(
			"menu-item-has-children\"><a",
			"menu-item-has-children\">^^<a",
			$output
		);

		$output = str_replace(
			"current-menu-item\"><a",
			"current-menu-item\">^^<a",
			$output
		);

		$output = preg_replace('/~~+/', '~', $output);
	}
}

