<?php

/**
 * Class WCML_Currency_Switcher_Ajax
 *
 */
class WCML_Currency_Switcher_Ajax{

	private $woocommerce_wpml;

	public function __construct( &$woocommerce_wpml ) {

		$this->woocommerce_wpml = $woocommerce_wpml;

		add_action( 'wp_ajax_wcml_currencies_order', array($this, 'wcml_currencies_order') );
		add_action( 'wp_ajax_wcml_currencies_switcher_preview', array($this, 'wcml_currencies_switcher_preview') );
		add_action( 'wp_ajax_wcml_currencies_switcher_save_settings', array($this, 'wcml_currencies_switcher_save_settings') );
		add_action( 'wp_ajax_wcml_delete_currency_switcher', array($this, 'wcml_delete_currency_switcher') );

	}

	public function wcml_currencies_order() {
		$nonce = array_key_exists( 'wcml_nonce', $_POST ) ? sanitize_text_field( $_POST['wcml_nonce'] ) : false;
		if ( !$nonce || !wp_verify_nonce( $nonce, 'set_currencies_order_nonce' ) ) {
			wp_send_json_error('Invalid nonce');
		}

		$this->woocommerce_wpml->settings['currencies_order'] = explode( ';', $_POST['order'] );
		$this->woocommerce_wpml->update_settings( $this->woocommerce_wpml->settings );
		wp_send_json_success( array( 'message' => esc_html__( 'Currencies order updated', 'woocommerce-multilingual' ) ) );
	}

	public function wcml_currencies_switcher_save_settings() {
		$nonce = array_key_exists( 'wcml_nonce', $_POST ) ? sanitize_text_field( $_POST['wcml_nonce'] ) : false;
		if ( !$nonce || !wp_verify_nonce( $nonce, 'wcml_currencies_switcher_save_settings' ) ) {
			wp_send_json_error('Invalid nonce');
		}
		$wcml_settings =& $this->woocommerce_wpml->settings;
		$switcher_settings = array();

		// Allow some HTML in the currency switcher
		$currency_switcher_format = strip_tags( stripslashes_deep( $_POST[ 'template' ] ), '<img><span><u><strong><em>');
		$currency_switcher_format = htmlentities( $currency_switcher_format );
		$currency_switcher_format = sanitize_text_field( $currency_switcher_format );
		$currency_switcher_format = html_entity_decode( $currency_switcher_format );

		$switcher_id = sanitize_text_field( $_POST[ 'switcher_id' ] );
		if( $switcher_id == 'new_widget' ){
			$switcher_id = sanitize_text_field( $_POST[ 'widget_id' ] );

		}

		$switcher_settings[ 'widget_title' ]   = isset( $_POST[ 'widget_title' ] ) ? sanitize_text_field( $_POST[ 'widget_title' ] ) : '';
		$switcher_settings[ 'switcher_style' ] = sanitize_text_field( $_POST[ 'switcher_style' ] );
		$switcher_settings[ 'template' ]       = $currency_switcher_format;

		do_action('wpml_register_single_string', 'woocommerce-multilingual',  $switcher_id .'_switcher_format' , $currency_switcher_format );

		foreach( $_POST[ 'color_scheme' ] as $color_id => $color ){
			$switcher_settings[ 'color_scheme' ][ sanitize_text_field( $color_id ) ] = sanitize_hex_color( $color );
		}

		$wcml_settings[ 'currency_switchers' ][ $switcher_id ] = $switcher_settings;

		//update widget settings
		if( $switcher_id != 'product' ){
			$widget_settings = get_option('widget_currency_sel_widget');
			$setting_match = false;
			foreach( $widget_settings as $key => $widget_setting ){
				if( isset( $widget_setting['id'] ) && $switcher_id == $widget_setting['id'] ){
					$setting_match = true;
					$widget_settings[ $key ][ 'settings' ] = $switcher_settings;
				}
			}

			if( !$setting_match ){
				$widget_settings[] = array(
					'id' => $switcher_id,
					'settings' => $switcher_settings
				);
			}

			update_option( 'widget_currency_sel_widget', $widget_settings );

			$this->synchronize_widget_instances( $widget_settings );
		}

		$this->woocommerce_wpml->update_settings( $wcml_settings );

		wp_send_json_success();
	}

	private function synchronize_widget_instances( $widget_settings ) {

		$sidebars_widgets = $this->get_sidebars_widgets();

		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( 'wp_inactive_widgets' === $sidebar ) {
				continue;
			}

			$found = false;
			if ( is_array( $widgets ) ) {
				foreach ( $widgets as $key => $widget_id ) {
					if ( strpos( $widget_id, WCML_Currency_Switcher_Widget::SLUG ) === 0 ) {

						if ( $found ) { // Only synchronize the first CS widget instance per sidebar
							unset( $sidebars_widgets[ $sidebar ][ $key ] );
							continue;
						}

						$found = true;

					}
				}
			}

			if ( ! $found ) {

				foreach( $widget_settings as $key => $widget_setting ){
					if( $widget_setting['id'] == $sidebar ){
						array_unshift( $sidebars_widgets[ $sidebar ], WCML_Currency_Switcher_Widget::SLUG.'-'.$key );
					}
				}

			}
		}

		$this->update_sidebars_widgets( $sidebars_widgets );

	}

	public function wcml_delete_currency_switcher(){
		$nonce = array_key_exists( 'wcml_nonce', $_POST ) ? sanitize_text_field( $_POST['wcml_nonce'] ) : false;
		if ( !$nonce || !wp_verify_nonce( $nonce, 'delete_currency_switcher' ) ) {
			wp_send_json_error();
		}

		$switcher_id = sanitize_text_field( $_POST[ 'switcher_id' ] );

		$wcml_settings = $this->woocommerce_wpml->get_settings();

		unset( $wcml_settings[ 'currency_switchers' ][ $switcher_id ] );

		$this->woocommerce_wpml->update_settings( $wcml_settings );

		$sidebars_widgets = $this->get_sidebars_widgets();

		foreach ($sidebars_widgets as $sidebar => $widgets) {
			if ($sidebar != $switcher_id) {
				continue;
			}

			if (is_array($widgets)) {
				foreach ($widgets as $key => $widget_id) {
					if (strpos($widget_id, WCML_Currency_Switcher_Widget::SLUG) === 0) {
						unset($sidebars_widgets[$sidebar][$key]);
					}
				}
			}
		}

		$this->update_sidebars_widgets( $sidebars_widgets );

		wp_send_json_success();
	}

	public function wcml_currencies_switcher_preview() {
		$nonce = array_key_exists( 'wcml_nonce', $_POST ) ? sanitize_text_field( $_POST['wcml_nonce'] ) : false;
		if ( !$nonce || !wp_verify_nonce( $nonce, 'wcml_currencies_switcher_preview' ) ) {
			wp_send_json_error( 'Invalid nonce' );
		}
		$return= array();

		$inline_css = $this->woocommerce_wpml->cs_templates->get_color_picket_css( $_POST['switcher_id'], array( 'switcher_style' => $_POST['switcher_style'], 'color_scheme' => $_POST['color_scheme'] ) );

		$template = $this->woocommerce_wpml->cs_templates->get_template( $_POST['switcher_style'] );

		if ( $template->has_styles() ) {
			$return['inline_styles_id'] = $template->get_inline_style_handler().'-inline-css';
		}else{
			$return['inline_styles_id'] = 'wcml-cs-inline-styles-' . $_POST['switcher_id'].'-'.$_POST['switcher_style'];
		}

		$return['inline_css'] = $inline_css;

		ob_start();
		$this->woocommerce_wpml->multi_currency->currency_switcher->wcml_currency_switcher(
			array(
				'switcher_id'	 => $_POST['switcher_id'],
				'format'         => isset( $_POST['template'] ) ? stripslashes_deep( $_POST['template'] ) : '%name% (%symbol%) - %code%',
				'switcher_style' => $_POST['switcher_style'],
				'color_scheme'   => $_POST['color_scheme'],
				'preview' => true
			)
		);
		$switcher_preview = ob_get_contents();
		ob_end_clean();

		$return['preview'] = $switcher_preview;

		wp_send_json_success( $return );
	}

	public function get_sidebars_widgets() {
		if ( ! function_exists( 'wp_get_sidebars_widgets' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/widgets.php' );
		}
		$sidebars_widgets = wp_get_sidebars_widgets();

		return is_array( $sidebars_widgets ) ? $sidebars_widgets : array();
	}
	public function update_sidebars_widgets( $sidebars_widgets ){
		remove_action( 'pre_update_option_sidebars_widgets', array( $this->woocommerce_wpml->multi_currency->currency_switcher, 'update_option_sidebars_widgets' ), 10, 2 );

		wp_set_sidebars_widgets( $sidebars_widgets );

		add_action( 'pre_update_option_sidebars_widgets', array( $this->woocommerce_wpml->multi_currency->currency_switcher, 'update_option_sidebars_widgets' ), 10, 2 );
	}
}
