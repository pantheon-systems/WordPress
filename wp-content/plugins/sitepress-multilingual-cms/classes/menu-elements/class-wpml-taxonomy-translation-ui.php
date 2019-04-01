<?php

/**
 * Class WPML_Taxonomy_Translation_UI
 */
class WPML_Taxonomy_Translation_UI {

	private $sitepress;
	private $taxonomy;
	private $tax_selector;
	private $screen_options;

	/**
	 * WPML_Taxonomy_Translation constructor.
	 *
	 * @param SitePress $sitepress
	 * @param string $taxonomy if given renders a specific taxonomy,
	 *                         otherwise renders a placeholder
	 * @param bool[] $args array with possible indices:
	 *                     'taxonomy_selector' => bool .. whether or not to show the taxonomy selector
	 * @param WPML_UI_Screen_Options_Factory $screen_options_factory
	 */
	public function __construct(
		SitePress $sitepress,
		$taxonomy = '',
		array $args = array(),
		WPML_UI_Screen_Options_Factory $screen_options_factory = null
	) {
		$this->sitepress    = $sitepress;
		$this->tax_selector = isset( $args['taxonomy_selector'] ) ? $args['taxonomy_selector'] : true;
		$this->taxonomy     = $taxonomy ? $taxonomy : false;

		if ( $screen_options_factory ) {
			$help_title = esc_html__( 'Taxonomy Translation', 'sitepress' );
			$help_text  = $this->get_help_text();

			$this->screen_options = $screen_options_factory->create_pagination( 'taxonomy_translation_per_page',
			                                                                    ICL_TM_DOCS_PER_PAGE );
			$screen_options_factory->create_help_tab( 'taxonomy_translation_help_tab',
			                                                                  $help_title,
			                                                                  $help_text );
		}
	}

	/**
	 * Echos the HTML that serves as an entry point for the taxonomy translation
	 * screen and enqueues necessary js.
	 */
	public function render() {
		WPML_Taxonomy_Translation_Table_Display::enqueue_taxonomy_table_js( $this->sitepress );
		$output = '<div class="wrap">';
		if ( $this->taxonomy ) {
			$output .= '<input type="hidden" id="tax-preselected" value="' . $this->taxonomy . '">';
		}
		if ( ! $this->tax_selector ) {
			$output .= '<input type="hidden" id="tax-selector-hidden" value="1"/>';
		}
		if ( $this->tax_selector ) {
			$output .= '<h1>' . esc_html__( 'Taxonomy Translation', 'sitepress' ) . '</h1>';
			$output .= '<br/>';
		}
		$output .= '<div id="wpml_tt_taxonomy_translation_wrap" data-items_per_page="'
		           . $this->get_items_per_page()
		           . '">';
		$output .= '<div class="loading-content"><span class="spinner" style="visibility: visible"></span></div>';
		$output .= '</div>';
		do_action( 'icl_menu_footer' );
		echo $output . '</div>';
	}

	/**
	 * @return int
	 */
	private function get_items_per_page() {
		$items_per_page = 10;
		if ( $this->screen_options ) {
			$items_per_page = $this->screen_options->get_items_per_page();
		}

		return $items_per_page;
	}

	/**
	 * @return string
	 */
	private function get_help_text() {
		/* translators: this is the title of a documentation page used to terminate the sentence "is not possible to ..."  */
		$translate_base_taxonomy_slug_link_title = esc_html__( 'translate the base taxonomy slugs with WPML',
		                                                       'sitepress' );
		$translate_base_taxonomy_slug_link       = '<a href="https://wpml.org/faq/translate-taxonomy-slugs-wpml/" target="_blank">'
		                                           . $translate_base_taxonomy_slug_link_title
		                                           . '</a>';

		/* translators: this is the title of a documentation page used to terminate the sentence "To learn more, please visit our documentation page about..."  */
		$translate_taxonomies_link_title = esc_html__( 'translating post categories and custom taxonomies',
		                                               'sitepress' );
		$translate_taxonomies_link       = '<a href="https://wpml.org/documentation/getting-started-guide/translating-post-categories-and-custom-taxonomies/" target="_blank">'
		                                   . $translate_taxonomies_link_title
		                                   . '</a>';

		$help_sentences   = array();
		$help_sentences[] = esc_html__( "WPML allows you to easily translate your site's taxonomies. Only taxonomies marked as translatable will be available for translation. Select the taxonomy in the dropdown menu and then use the list of taxonomy terms that appears to translate them.",
		                                'sitepress' );
		/* translators: the sentence is completed with "translate the base taxonomy slugs with WPML" */
		$help_sentences[] = sprintf( esc_html__( 'Please note that currently, you can translate the slugs of taxonomy terms but it is not possible to %s.',
		                                         'sitepress' ),
		                             $translate_base_taxonomy_slug_link );
		/* translators: the sentence is completed with "translating post categories and custom taxonomies" */
		$help_sentences[] = sprintf( esc_html__( 'To learn more, please visit our documentation page about %s.',
		                                         'sitepress' ),
		                             $translate_taxonomies_link );

		return '<p>' . implode( '</p><p>', $help_sentences ) . '</p>';
	}
}
