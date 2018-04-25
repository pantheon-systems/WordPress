<?php
/**
 * Server-side rendering of the `core/categories` block.
 *
 * @package gutenberg
 */

/**
 * Renders the `core/categories` block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the categories list/dropdown markup.
 */
function gutenberg_render_block_core_categories( $attributes ) {
	static $block_id = 0;
	$block_id++;

	$align = 'center';
	if ( isset( $attributes['align'] ) && in_array( $attributes['align'], array( 'left', 'right', 'full' ), true ) ) {
		$align = $attributes['align'];
	}

	$args = array(
		'echo'         => false,
		'hierarchical' => ! empty( $attributes['showHierarchy'] ),
		'orderby'      => 'name',
		'show_count'   => ! empty( $attributes['showPostCounts'] ),
		'title_li'     => '',
	);

	if ( ! empty( $attributes['displayAsDropdown'] ) ) {
		$id                       = 'wp-block-categories-' . $block_id;
		$args['id']               = $id;
		$args['show_option_none'] = __( 'Select Category', 'gutenberg' );
		$wrapper_markup           = '<div class="%1$s">%2$s</div>';
		$items_markup             = wp_dropdown_categories( $args );
		$type                     = 'dropdown';

		if ( ! is_admin() ) {
			$wrapper_markup .= gutenberg_build_dropdown_script_block_core_categories( $id );
		}
	} else {
		$wrapper_markup = '<div class="%1$s"><ul>%2$s</ul></div>';
		$items_markup   = wp_list_categories( $args );
		$type           = 'list';
	}

	$class = "wp-block-categories wp-block-categories-{$type} align{$align}";

	$block_content = sprintf(
		$wrapper_markup,
		esc_attr( $class ),
		$items_markup
	);

	return $block_content;
}

/**
 * Generates the inline script for a categories dropdown field.
 *
 * @param string $dropdown_id ID of the dropdown field.
 *
 * @return string Returns the dropdown onChange redirection script.
 */
function gutenberg_build_dropdown_script_block_core_categories( $dropdown_id ) {
	ob_start();
	?>
	<script type='text/javascript'>
	/* <![CDATA[ */
	(function() {
		var dropdown = document.getElementById( '<?php echo esc_js( $dropdown_id ); ?>' );
		function onCatChange() {
			if ( dropdown.options[ dropdown.selectedIndex ].value > 0 ) {
				location.href = "<?php echo home_url(); ?>/?cat=" + dropdown.options[ dropdown.selectedIndex ].value;
			}
		}
		dropdown.onchange = onCatChange;
	})();
	/* ]]> */
	</script>
	<?php
	return ob_get_clean();
}

register_block_type( 'core/categories', array(
	'render_callback' => 'gutenberg_render_block_core_categories',
) );
