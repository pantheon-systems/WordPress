<?php

/**
 * Class WPML_TM_Translation_Priorities_Register_Action
 *
 */
class WPML_TM_Translation_Priorities_Register_Action implements IWPML_Action {

	/** @var SitePress */
	private $sitepress;

	const TRANSLATION_PRIORITY_TAXONOMY = 'translation_priority';

	/**
	 * WPML_TM_Translation_Priorities_Register_Action constructor.
	 *
	 * @param SitePress $sitepress
	 */
	public function __construct( SitePress $sitepress ) {
		$this->sitepress        = $sitepress;
	}

	public function add_hooks() {
		add_action( 'init', array( $this, 'register_translation_priority_taxonomy' ), 5 );
	}

	public function register_translation_priority_taxonomy() {

		if ( ! is_blog_installed() ) {
			return;
		}

		register_taxonomy( self::TRANSLATION_PRIORITY_TAXONOMY,
			apply_filters( 'wpml_taxonomy_objects_translation_priority', array_keys( $this->sitepress->get_translatable_documents() ) ),
			apply_filters( 'wpml_taxonomy_args_translation_priority', array(
				'label'              => __( 'Translation Priority', 'sitepress' ),
				'labels'             => array(
					'name'          => __( 'Translation Priorities', 'sitepress' ),
					'singular_name' => __( 'Translation Priority', 'sitepress' ),
					'all_items'     => __( 'All Translation Priorities', 'sitepress' ),
					'edit_item'     => __( 'Edit Translation Priority', 'sitepress' ),
					'update_item'   => __( 'Update Translation Priority', 'sitepress' ),
					'add_new_item'  => __( 'Add new Translation Priority', 'sitepress' ),
					'new_item_name' => __( 'New Translation Priority Name', 'sitepress' ),
				),
				'hierarchical'       => false,
				'show_ui'            => true,
				'show_in_menu'       => false,
				'show_in_rest'       => false,
				'show_tagcloud'      => false,
				'show_in_quick_edit' => false,
				'show_admin_column'  => false,
				'query_var'          => is_admin(),
				'rewrite'            => false,
				'public'             => false,
				'meta_box_cb'        => false,
			) )
		);
	}
}

