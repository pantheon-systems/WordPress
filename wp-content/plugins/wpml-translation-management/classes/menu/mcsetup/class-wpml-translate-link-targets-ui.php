<?php

class WPML_Translate_Link_Targets_UI extends WPML_TM_MCS_Section_UI {
	const ID = 'ml-content-setup-sec-links-target';

	/** @var WPDB $wpdb */
	private $wpdb;
	/** @var WPML_Pro_Translation $pro_translation */
	private $pro_translation;
	/** @var  WPML_WP_API $wp_api */
	private $wp_api;
	/** @var  SitePress $sitepress */
	private $sitepress;

	public function __construct(  $title, $wpdb, $sitepress, $pro_translation ) {
		parent::__construct( self::ID, $title );
		$this->wpdb            = $wpdb;
		$this->pro_translation = $pro_translation;
		$this->sitepress       = $sitepress;
	}

	/**
	 * @return string
	 */
	protected function render_content() {
		$output = '';

		$main_message = __( 'Adjust links in posts so they point to the translated content', 'wpml-translation-management' );
		$complete_message = __( 'All posts have been processed. %s links were changed to point to the translated content.', 'wpml-translation-management' );
		$string_count = 0;

		$posts        = new WPML_Translate_Link_Targets_In_Posts_Global( new WPML_Translate_Link_Target_Global_State( $this->sitepress ), $this->wpdb, $this->pro_translation );
		$post_count   = $posts->get_number_to_be_fixed();

		if ( defined( 'WPML_ST_VERSION') ) {
			$strings      = new WPML_Translate_Link_Targets_In_Strings_Global(  new WPML_Translate_Link_Target_Global_State( $this->sitepress ), $this->wpdb, $this->wp_api, $this->pro_translation );
			$string_count = $strings->get_number_to_be_fixed();
			$main_message = __( 'Adjust links in posts and strings so they point to the translated content', 'wpml-translation-management' );
			$complete_message = __( 'All posts and strings have been processed. %s links were changed to point to the translated content.', 'wpml-translation-management' );
		}

		$data_attributes = array(
			'post-message'     => esc_attr__( 'Processing posts... %1$s of %2$s done.', 'wpml-translation-management' ),
			'post-count'       => $post_count,
			'string-message'   => esc_attr__( 'Processing strings... %1$s of %2$s done.', 'wpml-translation-management' ),
			'string-count'     => $string_count,
			'complete-message' => esc_attr( $complete_message ),
		);

		$output .= '<p>' . $main_message . '</p>';
		$output .= '<button id="wpml-scan-link-targets" class="button-secondary"';

		foreach ( $data_attributes as $key => $value ) {
			$output .= ' data-' . $key . '="' . $value . '"';
		}
		$output .= '>' . esc_html__( 'Scan now and adjust links', 'wpml-translation-management' ) . '</button>';
		$output .= '<span class="spinner"> </span>';
		$output .= '<p class="results"> </p>';
		$output .= wp_nonce_field( 'WPML_Ajax_Update_Link_Targets', 'wpml-translate-link-targets', true, false );

		return $output;
	}
}

