<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

class WPML_TM_Translate_Independently {

	/**	@var TranslationManagement $translation_management */
	private $translation_management;

	/** @var WPML_Translation_Basket $translation_basket */
	private $translation_basket;

	/** @var SitePress $sitepress */
	private $sitepress;

	public function __construct(
		TranslationManagement $translation_management,
		WPML_Translation_Basket $translation_basket,
		SitePress $sitepress
	) {
		$this->translation_management = $translation_management;
		$this->translation_basket     = $translation_basket;
		$this->sitepress              = $sitepress;
	}

	/**
	 * Init all plugin actions.
	 */
	public function init() {
		add_action( 'wp_ajax_icl_disconnect_posts', array( $this, 'ajax_disconnect_duplicates' ) );
		add_action( 'admin_footer', array( $this, 'add_hidden_field' ) );
	}

	/**
	 * Add hidden fields to TM basket.
	 * #icl_duplicate_post_in_basket with list of duplicated ids in basket target languages.
	 * #icl_disconnect_nonce nonce for AJAX call.
	 */
	public function add_hidden_field() {
		$basket = $this->translation_basket->get_basket( true );
		if ( ! isset( $basket['post'] ) ) {
			return;
		}
		$posts_ids_to_disconnect = $this->duplicates_to_disconnect( $basket['post'] );
		if ( $posts_ids_to_disconnect ) :
			?>
			<input type="hidden" value="<?php echo implode( ',', $posts_ids_to_disconnect ); ?>" id="icl_duplicate_post_in_basket">
			<input type="hidden" value="<?php echo wp_create_nonce( 'icl_disconnect_duplicates' ); ?>" id="icl_disconnect_nonce">
			<?php
		endif;
	}

	/**
	 * @param array $basket_posts
	 *
	 * @return array
	 */
	private function duplicates_to_disconnect( $basket_posts ) {
		/** @var SitePress $sitepress */
		global $sitepress;
		$posts_to_disconnect = array();

		foreach ( $basket_posts as $from_post => $data ) {
			$target_langs = array_keys( $data['to_langs'] );
			$element_type = 'post_' . get_post_type( $from_post );
			$trid         = $sitepress->get_element_trid( $from_post, $element_type );
			$translations = $sitepress->get_element_translations( $trid, $element_type );

			foreach ( $translations as $translation ) {
				if ( ! in_array( $translation->language_code, $target_langs, true ) ) {
					continue;
				}

				$is_duplicate = get_post_meta( $translation->element_id, '_icl_lang_duplicate_of', true );

				if ( $is_duplicate ) {
					$posts_to_disconnect[] = (int) $translation->element_id;
				}
			}
		}

		return $posts_to_disconnect;
	}

	/**
	 * AJAX action to bulk disconnect posts before sending them to translation.
	 */
	public function ajax_disconnect_duplicates() {
		// Check nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'icl_disconnect_duplicates' ) ) {
			wp_send_json_error( esc_html__( 'Failed to disconnect posts', 'wpml-translation-management' ) );
			return;
		}

		// Get post basket post ids.
		$post_ids = isset( $_POST['posts'] ) ? explode( ',', $_POST['posts'] ) : array();
		if ( empty( $post_ids ) ) {
			wp_send_json_error( esc_html__( 'No duplicate posts found to disconnect.', 'wpml-translation-management' ) );
			return;
		}

		$post_ids = array_map( 'intval', $post_ids );
		array_walk( $post_ids, array( $this->translation_management, 'reset_duplicate_flag' ) );

		wp_send_json_success( esc_html__( 'Successfully disconnected posts', 'wpml-translation-management' ) );
	}
}
