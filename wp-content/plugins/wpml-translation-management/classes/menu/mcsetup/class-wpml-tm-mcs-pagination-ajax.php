<?php

/**
 * Class WPML_TM_MCS_Pagination_Ajax
 */
class WPML_TM_MCS_Pagination_Ajax {

	/** @var TranslationManagement */
	private $icl_translation_management;

	/**
	 * WPML_TM_MCS_Pagination_Ajax constructor.
	 *
	 * @param TranslationManagement $icl_translation_management
	 */
	public function __construct( TranslationManagement $icl_translation_management ) {
		$this->icl_translation_management = $icl_translation_management;
	}

	/**
	 * Define Ajax hooks.
	 */
	public function add_hooks() {
		add_action( 'wp_ajax_wpml_update_mcs_cf', array( $this, 'update_mcs_cf' ) );
	}

	/**
	 * Update custom fields form.
	 */
	public function update_mcs_cf() {
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'icl_' . $_POST['type'] . '_translation_nonce' ) ) {
			$items_per_page     = intval( $_POST['items_per_page'] );
			$paged              = intval( $_POST['paged'] );
			$search_string      = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
			$show_system_fields = isset( $_POST['show_system_fields'] ) && filter_var( $_POST['show_system_fields'], FILTER_VALIDATE_BOOLEAN );

			$settings_factory = new WPML_Custom_Field_Setting_Factory( $this->icl_translation_management );

			$settings_factory->show_system_fields = $show_system_fields;

			$unlock_button_ui = new WPML_UI_Unlock_Button();

			if ( 'cf' === $_POST['type'] ) {
				$menu_item = new WPML_TM_MCS_Post_Custom_Field_Settings_Menu( $settings_factory, $unlock_button_ui );
			} elseif ( 'tcf' === $_POST['type'] ) {
				$menu_item = new WPML_TM_MCS_Term_Custom_Field_Settings_Menu( $settings_factory, $unlock_button_ui );
			}

			$result = array();
			ob_start();
			$menu_item->render_body( $menu_item->paginate_keys( $items_per_page, $paged, $search_string ) );
			$result['body'] = ob_get_clean();

			ob_start();
			$menu_item->render_pagination( $items_per_page, $paged );
			$result['pagination'] = ob_get_clean();

			wp_send_json_success( $result );
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request.', 'wpml-translation-management' ),
				)
			);
		}
	}
}
