<?php
/**
 * @var SitePress $sitepress
 * @var wpdb $wpdb
 * @var Object $term
 *
 */

require dirname( __FILE__ ) . '/wpml-taxonomy-element-language-dropdown.class.php';

global $sitepress, $wpdb;

$sitepress->noscript_notice();

$element_id = isset( $term->term_taxonomy_id ) ? $term->term_taxonomy_id : false;

$element_type = isset( $_GET[ 'taxonomy' ] ) ? esc_sql( $_GET[ 'taxonomy' ] ) : 'post_tag';
$icl_element_type = 'tax_' . $element_type;

$default_language = $sitepress->get_default_language();
$current_language = $sitepress->get_current_language();

if ( $element_id ) {
	$res_prepared = $wpdb->prepare( "SELECT trid, language_code, source_language_code
				  FROM {$wpdb->prefix}icl_translations WHERE element_id=%d AND element_type=%s", array( $element_id, $icl_element_type ) );
	$res          = $wpdb->get_row( $res_prepared );
	$trid         = $res->trid;
	if ( $trid ) {
		$element_lang_code = $res->language_code;
	} else {
		$element_lang_code = $current_language;

		$translation_id = $sitepress->set_element_language_details( $element_id, $icl_element_type, null, $element_lang_code );
		//get trid of $translation_id
		$trid = $wpdb->get_var( $wpdb->prepare( "SELECT trid FROM {$wpdb->prefix}icl_translations WHERE translation_id=%d", array( $translation_id) ) );
	}
} else {
	$trid = isset( $_GET['trid'] ) ? (int) $_GET['trid'] : false;

	$element_lang_code = $current_language;
	if( array_key_exists( 'lang', $_GET ) ) {
		$element_lang_code = filter_var( $_GET['lang'], FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	}
}

$translations = false;
if ( $trid ) {
	$translations = $sitepress->get_element_translations( $trid, $icl_element_type );
}
$terms_translations = empty( $translations ) ? array() : $translations;

$active_languages = $sitepress->get_active_languages();
$selected_language = $element_lang_code ? $element_lang_code : $default_language;
$source_language = isset( $_GET[ 'source_lang' ] ) ? strip_tags( filter_input ( INPUT_GET, 'source_lang', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) : false;
$untranslated_ids = $sitepress->get_elements_without_translations( $icl_element_type, $selected_language, $default_language );

$dropdown = new WPML_Taxonomy_Element_Language_Dropdown();
$dropdown->add_language_selector_to_page (
	$active_languages,
	$selected_language,
  $terms_translations,
	$element_id,
	$icl_element_type
);

$setup_complete = $sitepress->get_setting( 'setup_complete' );

if ( $setup_complete ) {
    require WPML_PLUGIN_PATH . '/menu/wpml-translation-selector.class.php';
    $selector = new WPML_Translation_Selector( $sitepress, $default_language, $source_language, $element_id );
    $selector->add_translation_of_selector_to_page (
        $trid,
        $sitepress->get_current_language (),
        $selected_language,
        $untranslated_ids
    );
$sitepress->add_translate_options( $trid, $active_languages, $selected_language, $terms_translations, $icl_element_type );

?>
</div></div></div></div></div>
<?php
	if ( $trid && $sitepress->get_wp_api()->is_term_edit_page() ) {
	/**
	 * Extends the translation options for terms
	 *
	 * Called after rendering the translation options for terms, after the closing the main container tag
	 *
	 * @since 3.8.2
	 *
	 * @param array $args              {
	 *                                 Information about the current term and its translations
	 *
	 * @type int    $trid              The translation cluster ID.
	 * @type array  $active_languages  All active languages data.
	 * @type string $selected_language The language of the current term being edited.
	 * @type array  $translations      All the available translations (including the current one).
	 * @type string $type              The translation element type (e.g. `tax_category`, `tax_{taxonomy}`.
	 * }
	 */
	do_action( 'wpml_translate_options_terms_after', array( 'trid' => $trid, 'active_languages' => $active_languages, 'selected_language' => $selected_language, 'translations' => $terms_translations, 'type' => $icl_element_type ) );
}
}
