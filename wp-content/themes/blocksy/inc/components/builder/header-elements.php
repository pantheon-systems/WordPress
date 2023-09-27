<?php

class Blocksy_Header_Builder_Elements {
	private $current_section_id = null;

	public function __construct($args = []) {
		$args = wp_parse_args($args, [
			'current_section_id' => null
		]);

		$this->current_section_id = $args['current_section_id'];
	}

	public function render_offcanvas($args = []) {
		$args = wp_parse_args($args, [
			'has_container' => true,
			'device' => 'mobile'
		]);

		$render = new Blocksy_Header_Builder_Render([
			'current_section_id' => $this->current_section_id
		]);

		if (! $render->contains_item('trigger')) {
			if (! is_customize_preview()) {
				return '';
			}
		}

		$mobile_content = '';
		$desktop_content = '';

		$current_layout = $render->get_current_section()['mobile'];

		foreach ($current_layout as $row) {
			if ($row['id'] !== 'offcanvas') {
				continue;
			}

			if ($render->is_row_empty($row)) {
				// return '';
			}

			$mobile_content .= $render->render_items_collection(
				$row['placements'][0]['items'],
				[
					'device' => 'mobile'
				]
			);
		}

		$current_layout = $render->get_current_section()['desktop'];

		foreach ($current_layout as $row) {
			if ($row['id'] !== 'offcanvas') {
				continue;
			}

			if (! empty($desktop_content)) {
				continue;
			}

			$desktop_content = $render->render_items_collection(
				$row['placements'][0]['items']
			);
		}

		$atts = $render->get_item_data_for('offcanvas');
		$row_config = $render->get_item_config_for('offcanvas');

		$class = 'ct-panel ct-header';
		$behavior = 'modal';

		$position_output = [];

		if (blocksy_default_akg('offcanvas_behavior', $atts, 'panel') !== 'modal') {
			$behavior = blocksy_default_akg(
				'side_panel_position', $atts, 'right'
			) . '-side';
		}

		ob_start();
		do_action('blocksy:header:offcanvas:desktop:top');
		$desktop_content = ob_get_clean() . $desktop_content;

		ob_start();
		do_action('blocksy:header:offcanvas:desktop:bottom');
		$desktop_content = $desktop_content . ob_get_clean();

		ob_start();
		do_action('blocksy:header:offcanvas:mobile:top');
		$mobile_content = ob_get_clean() . $mobile_content;

		ob_start();
		do_action('blocksy:header:offcanvas:mobile:bottom');
		$mobile_content = $mobile_content . ob_get_clean();

		$without_container = blocksy_html_tag(
			'div',
			array_merge(
				[
					'class' => 'ct-panel-content',
					'data-device' => 'desktop'
				],
				is_customize_preview() ? [
					'data-item-label' => $row_config['config']['name'],
					'data-location' => $render->get_customizer_location_for('offcanvas')
				] : []
			),
			$desktop_content
		) . blocksy_html_tag(
			'div',
			array_merge(
				[
					'class' => 'ct-panel-content',
					'data-device' => 'mobile'
				],
				is_customize_preview() ? [
					'data-item-label' => $row_config['config']['name'],
					'data-location' => $render->get_customizer_location_for('offcanvas')
				] : []
			),
			$mobile_content
		);

		$close_type = blocksy_akg('menu_close_button_type', $atts, 'type-1');

		$main_offcanvas_close_icon = apply_filters(
			'blocksy:main:offcanvas:close:icon',
			'<svg class="ct-icon" width="12" height="12" viewBox="0 0 15 15"><path d="M1 15a1 1 0 01-.71-.29 1 1 0 010-1.41l5.8-5.8-5.8-5.8A1 1 0 011.7.29l5.8 5.8 5.8-5.8a1 1 0 011.41 1.41l-5.8 5.8 5.8 5.8a1 1 0 01-1.41 1.41l-5.8-5.8-5.8 5.8A1 1 0 011 15z"/></svg>'
		);

		$without_container = '
		<div class="ct-panel-actions">
			<button class="ct-toggle-close" data-type="' . $close_type . '" aria-label="'. __('Close drawer', 'blocksy') . '">
				'. $main_offcanvas_close_icon . '
			</button>
		</div>
		' .  $without_container;

		if (blocksy_default_akg(
			'offcanvas_behavior',
			$atts,
			'panel'
		) === 'panel') {
			$without_container = '<div class="ct-panel-inner">' . $without_container . '</div>';
		}

		if (! $args['has_container']) {
			return $without_container;
		}

		return blocksy_html_tag(
			'div',
			array_merge(
				[
					'id' => 'offcanvas',
					'class' => $class,
					'data-behaviour' => $behavior
					// ,
					// 'data-device' => $args['device']
				],
				$position_output
			),
			$without_container
		);
	}

	public function render_search_modal() {
		$render = new Blocksy_Header_Builder_Render([
			'current_section_id' => $this->current_section_id
		]);

		if (! $render->contains_item('search')) {
			return;
		}

		$atts = $render->get_item_data_for('search');

		$search_through = blocksy_akg('search_through', $atts, [
			'post' => true,
			'page' => true,
			'product' => true
		]);

		$section_id = $render->get_current_section_id();
		$key = 'header:' . $section_id . ':search:header_search_placeholder';

		$search_placeholder = blocksy_translate_dynamic(
			blocksy_akg(
				'header_search_placeholder',
				$atts,
				__('Search', 'blocksy')
			),
			$key
		);

		$search_close_button_type = blocksy_akg(
			'search_close_button_type',
			$atts,
			'type-1'
		);

		$all_cpts = blocksy_manager()->post_types->get_supported_post_types();

		if (function_exists('is_bbpress')) {
			$all_cpts[] = 'forum';
			$all_cpts[] = 'topic';
			$all_cpts[] = 'reply';
		}

		foreach ($all_cpts as $single_cpt) {
			if (! isset($search_through[$single_cpt])) {
				$search_through[$single_cpt] = true;
			}
		}

		$post_type = [];

		foreach ($search_through as $single_post_type => $enabled) {
			if (
				! $enabled
				||
				! get_post_type_object($single_post_type)
			) {
				continue;
			}

			if (
				$single_post_type !== 'post'
				&&
				$single_post_type !== 'page'
				&&
				$single_post_type !== 'product'
				&&
				! in_array($single_post_type, $all_cpts)
			) {
				continue;
			}

			$post_type[] = $single_post_type;
		}

		$search_modal_close_icon = apply_filters(
			'blocksy:search:modal:close:icon',
			'<svg class="ct-icon" width="12" height="12" viewBox="0 0 15 15"><path d="M1 15a1 1 0 01-.71-.29 1 1 0 010-1.41l5.8-5.8-5.8-5.8A1 1 0 011.7.29l5.8 5.8 5.8-5.8a1 1 0 011.41 1.41l-5.8 5.8 5.8 5.8a1 1 0 01-1.41 1.41l-5.8-5.8-5.8 5.8A1 1 0 011 15z"/></svg>'
		);

		$search_form_args = [
			'enable_search_field_class' => true,
			'ct_post_type' => $post_type,
			'search_placeholder' => $search_placeholder,
			'search_live_results' => 'no'
		];

		if (blocksy_akg('enable_live_results', $atts, 'yes') === 'yes') {
			$search_form_args['search_live_results'] = 'yes';

			$search_form_args['live_results_attr'] = blocksy_akg(
				'searchHeaderImages', $atts, 'yes'
			) === 'yes' ? 'thumbs' : '';

			$search_form_args['ct_product_price'] = blocksy_akg(
				'searchHeaderProductPrice', $atts, 'no'
			) === 'yes';
		}

		?>

		<div id="search-modal" class="ct-panel" data-behaviour="modal">
			<div class="ct-panel-actions">
				<button class="ct-toggle-close" data-type="<?php echo $search_close_button_type ?>" aria-label="Close search modal">
					<?php echo $search_modal_close_icon ?>
				</button>
			</div>

			<div class="ct-panel-content">
				<?php get_search_form($search_form_args); ?>
			</div>
		</div>

		<?php
	}

	public function render_cart_offcanvas($args = []) {
		$args = wp_parse_args($args, [
			'has_container' => true,
			'device' => 'mobile',
			'force_output' => false
		]);

		$render = new Blocksy_Header_Builder_Render([
			'current_section_id' => $this->current_section_id
		]);

		if (! $args['force_output']) {
			if (! $render->contains_item('cart')) {
				return '';
			}
		}

		if (! function_exists('woocommerce_mini_cart')) {
			return '';
		}

		$atts = $render->get_item_data_for('cart');

		$has_cart_dropdown = blocksy_default_akg(
			'has_cart_dropdown',
			$atts,
			'yes'
		) === 'yes';

		$cart_drawer_type = blocksy_default_akg('cart_drawer_type', $atts, 'dropdown');
		$cart_panel_close_button_type = blocksy_default_akg(
			'cart_panel_close_button_type',
			$atts,
			'type-1'
		);

		if (! $has_cart_dropdown) {
			return;
		}

		if ($cart_drawer_type !== 'offcanvas' && ! $args['force_output']) {
			return;
		}

		if (blocksy_default_akg('has_cart_panel_quantity', $atts, 'no') === 'yes') {
			add_filter(
				'woocommerce_widget_cart_item_quantity',
				[$this, 'add_minicart_quantity_fields'],
				10, 3
			);
		}

		global $blocksy_is_offcanvas_cart;
		$blocksy_is_offcanvas_cart = true;

		ob_start();
		woocommerce_mini_cart();
		$content = ob_get_clean();

		remove_filter(
			'woocommerce_widget_cart_item_quantity',
			[$this, 'add_minicart_quantity_fields'],
			10, 3
		);

		$class = 'ct-panel';
		$behavior = 'modal';

		$position_output = [];

		if (blocksy_default_akg('offcanvas_behavior', $atts, 'panel') !== 'modal') {
			$behavior = blocksy_default_akg(
				'cart_panel_position',
				$atts,
				'right'
			) . '-side';
		}

		$without_container = blocksy_html_tag(
			'div',
			array_merge([
				'class' => 'ct-panel-content',
			]),
			$content
		);

		if (! $args['has_container']) {
			return $without_container;
		}

		$cart_offcanvas_close_icon = apply_filters(
			'blocksy:cart:offcanvas:close:icon',
			'<svg class="ct-icon" width="12" height="12" viewBox="0 0 15 15"><path d="M1 15a1 1 0 01-.71-.29 1 1 0 010-1.41l5.8-5.8-5.8-5.8A1 1 0 011.7.29l5.8 5.8 5.8-5.8a1 1 0 011.41 1.41l-5.8 5.8 5.8 5.8a1 1 0 01-1.41 1.41l-5.8-5.8-5.8 5.8A1 1 0 011 15z"/></svg>'
		);

		return blocksy_html_tag(
			'div',
			array_merge(
				[
					'id' => 'woo-cart-panel',
					'class' => $class,
					'data-behaviour' => $behavior
				],
				$position_output
			),

			'<div class="ct-panel-inner">
				<div class="ct-panel-actions">
					<span class="ct-panel-heading">' . __('Shopping Cart', 'blocksy') . '</span>

					<button class="ct-toggle-close" data-type="' . $cart_panel_close_button_type . '" aria-label="' . __('Close cart drawer', 'blocksy') . '">
						'. $cart_offcanvas_close_icon . '
					</button>
				</div>
			'
			. $without_container .

			'</div>'
		);
	}

	public function add_minicart_quantity_fields($html, $cart_item, $cart_item_key) {
		$_product = apply_filters(
			'woocommerce_cart_item_product',
			$cart_item['data'],
			$cart_item,
			$cart_item_key
		);
		$product_price = apply_filters(
			'woocommerce_cart_item_price',
			WC()->cart->get_product_price($cart_item['data']),
			$cart_item,
			$cart_item_key
		);

		if ($_product->is_sold_individually()) {
			$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1">', $cart_item_key );
		} else {
			$product_quantity = trim(woocommerce_quantity_input(
				array(
					'input_name'   => "cart[{$cart_item_key}][qty]",
					'input_value'  => $cart_item['quantity'],
					'max_value'    => $_product->get_max_purchase_quantity(),
					'min_value'    => '0',
					'product_name' => $_product->get_name(),
				),
				$_product,
				false
			));
		}

		return '<div class="ct-product-actions">' . $product_quantity . '<span class="multiply-symbol">×</span>' . $product_price . '</div>';
	}
}
