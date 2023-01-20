<?php

if (! function_exists('blocksy_output_drawer_canvas')) {
	function blocksy_output_drawer_canvas() {
		$default_footer_elements = [];

		$elements = new Blocksy_Header_Builder_Elements();

		global $blocksy_has_default_header;

		if (
			isset($blocksy_has_default_header)
			&&
			$blocksy_has_default_header
		) {
			ob_start();
			$elements->render_search_modal();
			$default_footer_elements[] = ob_get_clean();

			$default_footer_elements[] = $elements->render_cart_offcanvas();
			$default_footer_elements[] = $elements->render_offcanvas();
		}

		if (get_theme_mod('has_back_top', 'no') === 'yes') {
			ob_start();
			blocksy_output_back_to_top_link();
			$default_footer_elements[] = ob_get_clean();
		}

		$footer_elements = apply_filters(
			'blocksy:footer:offcanvas-drawer',
			$default_footer_elements,
			$blocksy_has_default_header
		);

		if (function_exists('blocksy_woo_floating_cart')) {
			$maybe_floating_cart = blocksy_woo_floating_cart();

			if (! empty($maybe_floating_cart)) {
				$footer_elements[] = $maybe_floating_cart;
			}
		}

		if (! empty($footer_elements)) {
			echo '<div class="ct-drawer-canvas">';

			foreach ($footer_elements as $footer_el) {
				echo $footer_el;
			}

			echo '</div>';
		}
	}
}

add_action('wp_body_open', function () {
	if (! is_admin()) {
		blocksy_output_drawer_canvas();
	}
});

