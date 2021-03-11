<?php
/**
 * Menu editor handler
 *
 */

/**
 * Nav menu admin
 */
final class OE_Menu_Icons_Picker {

	/**
	 * Initialize class
	 *
	 */
	public static function init() {
		add_action( 'load-nav-menus.php', array( __CLASS__, '_load_nav_menus' ) );
		add_filter( 'wp_nav_menu_item_custom_fields', array( __CLASS__, '_fields' ), 10, 4 );
		add_filter( 'manage_nav-menus_columns', array( __CLASS__, '_columns' ), 99 );
		add_action( 'wp_update_nav_menu_item', array( __CLASS__, '_save' ), 10, 3 );
		add_filter( 'oe_icon_picker_type_props', array( __CLASS__, '_add_extra_type_props_data' ), 10, 3 );
	}

	/**
	 * Load Icon Picker
	 *
	 */
	public static function _load_nav_menus() {
		OE_Icon_Selector::instance()->load();

		add_action( 'print_media_templates', array( __CLASS__, '_media_templates' ) );
	}

	/**
	 * Get menu item setting fields
	 *
	 */
	protected static function _get_menu_item_fields( $meta ) {
		$fields = array_merge(
			array(
				array(
					'id'    => 'type',
					'label' => __( 'Type' ),
					'value' => $meta['type'],
				),
				array(
					'id'    => 'icon',
					'label' => __( 'Icon' ),
					'value' => $meta['icon'],
				),
			),
			OE_Menu_Icons_Settings::get_settings_fields( $meta )
		);

		return $fields;
	}

	/**
	 * Print fields
	 *
	 */
	public static function _fields( $id, $item, $depth, $args ) {
		$input_id      = sprintf( 'oe-icons-%d', $item->ID );
		$input_name    = sprintf( 'oe-icons[%d]', $item->ID );
		$menu_settings = OE_Menu_Icons_Settings::get_menu_settings( OE_Menu_Icons_Settings::get_current_menu_id() );
		$meta          = OE_Menu_Icons_Meta::get( $item->ID, $menu_settings );
		$fields        = self::_get_menu_item_fields( $meta );
		?>
			<div class="field-icon description-wide oe-icons-wrap" data-id="<?php echo json_encode( $item->ID ); ?>">
				<?php
					/**
					 * Add filter to allow to inject HTML before menu icons fields
					 */
					do_action( 'oe_menu_icons_before_fields', $item, $depth, $args, $id );
				?>
				<p class="description submitbox">
					<label><?php esc_html_e( 'Icon:', 'ocean-extra' ) ?></label>
					<?php printf( '<a class="_select">%s</a>', esc_html__( 'Select', 'ocean-extra' ) ); ?>
					<?php printf( '<a class="_remove submitdelete hidden">%s</a>', esc_html__( 'Remove', 'ocean-extra' ) ); ?>
				</p>
				<div class="_settings hidden">
					<?php
					foreach ( $fields as $field ) {
						printf(
							'<label>%1$s: <input type="text" name="%2$s" class="_oe-%3$s" value="%4$s" /></label><br />',
							esc_html( $field['label'] ),
							esc_attr( "{$input_name}[{$field['id']}]" ),
							esc_attr( $field['id'] ),
							esc_attr( $field['value'] )
						);
					}

					// The fields below will not be saved. They're only used for the preview.
					printf( '<input type="hidden" class="_oe-url" value="%s" />', esc_attr( $meta['url'] ) );
					?>
				</div>
				<?php
					/**
					 * Add filter to allow to inject HTML after menu icons fields
					 */
					do_action( 'oe_menu_icons_after_fields', $item, $depth, $args, $id );
				?>
			</div>
		<?php
	}

	/**
	 * Add our field to the screen options toggle
	 *
	 */
	public static function _columns( $columns ) {
		$columns['icon'] = __( 'Icon', 'ocean-extra' );

		return $columns;
	}

	/**
	 * Save menu item's icons metadata
	 *
	 */
	public static function _save( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen instanceof WP_Screen || 'nav-menus' !== $screen->id ) {
			return;
		}

		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		// Sanitize
		if ( ! empty( $_POST['oe-icons'][ $menu_item_db_id ] ) ) {
			$value = array_map(
				'sanitize_text_field',
				wp_unslash( (array) $_POST['oe-icons'][ $menu_item_db_id ] )
			);
		} else {
			$value = array();
		}

		OE_Menu_Icons_Meta::update( $menu_item_db_id, $value );
	}

	/**
	 * Get and print media templates from all types
	 *
	 */
	public static function _media_templates() {
		$id_prefix = 'tmpl-oe-icons';

		require_once dirname( __FILE__ ) . '/media-template.php';
	}

	/**
	 * Print media template
	 *
	 */
	protected static function _print_tempate( $id, $template ) {
		?>
			<script type="text/html" id="<?php echo esc_attr( $id ) ?>">
				<?php echo $template; // xss ok ?>
			</script>
		<?php
	}

	/**
	 * Add extra icon type properties data
	 *
	 */
	public static function _add_extra_type_props_data( $props, $id, $type ) {
		$settings_fields = array(
			'hide_label',
			'position',
			'vertical_align',
		);

		if ( 'Font' === $props['controller'] ) {
			$settings_fields[] = 'font_size';
		}

		switch ( $id ) {
			case 'image':
				$settings_fields[] = 'image_size';
				break;
			case 'svg':
				$settings_fields[] = 'svg_width';
				break;
		}

		$props['data']['settingsFields'] = $settings_fields;

		return $props;
	}
}