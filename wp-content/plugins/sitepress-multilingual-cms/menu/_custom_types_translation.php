<?php
if ( !isset( $wpdb ) ) {
	global $wpdb;
}
if ( !isset( $sitepress_settings ) ) {
	global $sitepress_settings;
}
if ( !isset( $sitepress ) ) {
	global $sitepress;
}
if ( !isset( $iclTranslationManagement ) ) {
	global $iclTranslationManagement;
}
global $wp_taxonomies;

if ( ! function_exists( 'prepare_synchronization_needed_warning' ) ) {
	function prepare_synchronization_needed_warning( $elements, $type ) {
		$notice = '';
		if ( $elements ) {
			$msg = esc_html( __( "You haven't set your synchronization preferences for these %s: %s. Default value was selected.", 'sitepress' ) );
			$notice .= '<div class="updated below-h2"><p>';
			$notice .= sprintf( $msg, $type, '<i>' . implode( '</i>, <i>', $elements ) . '</i>' );
			$notice .= '</p></div>';
		}

		return $notice;
	}
}

$default_language = $sitepress->get_default_language();

$wpml_post_types = new WPML_Post_Types( $sitepress );
$custom_posts = $wpml_post_types->get_translatable_and_readonly();

$custom_posts_sync_not_set = array();
foreach ( $custom_posts as $k => $custom_post ) {
	if ( !isset( $sitepress_settings[ 'custom_posts_sync_option' ][ $k ] ) ) {
		$custom_posts_sync_not_set[ ] = $custom_post->labels->name;
	}
}

$custom_taxonomies = array_diff( array_keys( (array) $wp_taxonomies ), array( 'nav_menu', 'link_category', 'post_format' ) );

$tax_sync_not_set = array();
foreach ( $custom_taxonomies as $custom_tax ) {
	if ( !isset( $sitepress_settings[ 'taxonomies_sync_option' ][ $custom_tax ] ) ) {
		$tax_sync_not_set[ ] = $wp_taxonomies[ $custom_tax ]->label;
	}
}

$translation_modes = new WPML_Translation_Modes();

$custom_types_ui = new WPML_Custom_Types_Translation_UI( $translation_modes, new WPML_UI_Unlock_Button() );

$CPT_slug_UI = $taxonomy_slug_UI = null;
if ( class_exists( 'WPML_ST_Slug_Translation_UI_Factory' ) ) {
	$slug_ui_factory  = new WPML_ST_Slug_Translation_UI_Factory();
	$CPT_slug_UI      = $slug_ui_factory->create( WPML_ST_Slug_Translation_UI_Factory::POST )->init();
	$taxonomy_slug_UI = $slug_ui_factory->create( WPML_ST_Slug_Translation_UI_Factory::TAX )->init();
}

if ( $custom_posts ) {
	$notice = prepare_synchronization_needed_warning( $custom_posts_sync_not_set, 'custom posts' );

	?>

    <div class="wpml-section" id="ml-content-setup-sec-7">

        <div class="wpml-section-header">
            <h3><?php esc_html_e( 'Post Types Translation', 'sitepress' );?></h3>
        </div>

        <div class="wpml-section-content wpml-section-content-wide">

            <?php
            	if ( isset( $notice ) ) {
            		echo $notice;
            	}

            ?>

            <form id="icl_custom_posts_sync_options" name="icl_custom_posts_sync_options"
				  class="js_element_type_sync_options" action="">
				<?php wp_nonce_field('icl_custom_posts_sync_options_nonce', '_icl_nonce') ?>

	            <div class="wpml-flex-table wpml-translation-setup-table wpml-margin-top-sm">
		            <?php $custom_types_ui->render_custom_types_header_ui( esc_html__( 'Post types', 'sitepress' ) ); ?>
		            <div class="wpml-flex-table-body">
		            <?php foreach ( $custom_posts as $k => $custom_post ):
			            $disabled = isset( $iclTranslationManagement->settings['custom-types_readonly_config'][ $k ] );

			            $translation_mode = WPML_CONTENT_TYPE_DONT_TRANSLATE;
			            if ( isset( $sitepress_settings['custom_posts_sync_option'][ $k ] ) ) {
				            $translation_mode = (int) $sitepress_settings['custom_posts_sync_option'][ $k ];
			            }
			            $unlocked = false;
			            if ( isset( $sitepress_settings['custom_posts_unlocked_option'][ $k ] ) ) {
				            $unlocked = (int) $sitepress_settings['custom_posts_unlocked_option'][ $k ];
			            }
			            ?>

			            <div class="wpml-flex-table-row wpml-flex-table-row-wrap js-type-translation-row">
				            <?php
				            $custom_types_ui->render_row(
					            esc_html( $custom_post->labels->name ),
					            'icl_sync_custom_posts',
					            $k,
					            $disabled,
					            $translation_mode,
					            $unlocked
				            );
				            if ( $CPT_slug_UI ) { ?>
					            <div class="wpml-flex-table-cell-span">
						            <?php echo $CPT_slug_UI->render( $k, $custom_post ); ?>
					            </div>
				            <?php } ?>
			            </div>
		            <?php endforeach; ?>
		            </div>
	            </div>

                <p class="buttons-wrap">
                    <span class="icl_ajx_response" id="icl_ajx_response_cp"></span>
                    <input type="submit"
						   class="js_element_type_sync_button button button-primary"
						   value="<?php esc_attr_e( 'Save', 'sitepress' ) ?>" />
                </p>

            </form>

        </div> <!-- .wpml-section-content -->

    </div> <!-- wpml-section -->

<?php
}

if ( $custom_taxonomies ) {
	$notice = prepare_synchronization_needed_warning( $tax_sync_not_set, 'taxonomies' );

?>
	<div class="wpml-section" id="ml-content-setup-sec-8">

	    <div class="wpml-section-header">
	        <h3><?php esc_html_e( 'Taxonomies Translation', 'sitepress' ); ?></h3>
	    </div>

	    <div class="wpml-section-content wpml-section-content-wide">

		    <?php
		    if ( isset( $notice ) ) {
			    echo $notice;
		    }

		    ?>

		    <form id="icl_custom_tax_sync_options" name="icl_custom_tax_sync_options"
				  class="js_element_type_sync_options" action="">
	            <?php wp_nonce_field('icl_custom_tax_sync_options_nonce', '_icl_nonce') ?>

			    <div class="wpml-flex-table wpml-translation-setup-table wpml-margin-top-sm">
				    <?php $custom_types_ui->render_custom_types_header_ui( esc_html__( 'Taxonomy', 'sitepress' ) ); ?>
				    <div class="wpml-flex-table-body">
					    <?php foreach ( $custom_taxonomies as $ctax ):
						    $disabled = isset( $iclTranslationManagement->settings['taxonomies_readonly_config'][ $ctax ] );

						    $translation_mode = WPML_CONTENT_TYPE_DONT_TRANSLATE;
						    if ( isset( $sitepress_settings['taxonomies_sync_option'][ $ctax ] ) ) {
							    $translation_mode = (int) $sitepress_settings['taxonomies_sync_option'][ $ctax ];
						    }
						    $unlocked = false;
						    if ( isset( $sitepress_settings['taxonomies_unlocked_option'][ $ctax ] ) ) {
							    $unlocked = (int) $sitepress_settings['taxonomies_unlocked_option'][ $ctax ];
						    }
						    ?>
						    <div class="wpml-flex-table-row wpml-flex-table-row-wrap js-type-translation-row">
							    <?php
							    $custom_types_ui->render_row(
								    esc_html( $wp_taxonomies[ $ctax ]->label ),
								    'icl_sync_tax',
								    $ctax,
								    $disabled,
								    $translation_mode,
								    $unlocked
							    );

							    $slug_UI = apply_filters( 'wpml_taxonomy_slug_translation_ui', $taxonomy_slug_UI, $ctax );

							    if ( $slug_UI ) { ?>
								<div class="wpml-flex-table-cell-span">
									<?php echo $slug_UI->render( $ctax, $wp_taxonomies[ $ctax ] ); ?>
								</div>
							   <?php } ?>
						    </div>
					    <?php endforeach; ?>
				    </div>
			    </div>
	            <p class="buttons-wrap">
	                <span class="icl_ajx_response" id="icl_ajx_response_ct"></span>
	                <input type="submit"
						   class="js_element_type_sync_button button-primary"
						   value="<?php esc_html_e( 'Save', 'sitepress' ) ?>" />
	            </p>
	        </form>
	    </div> <!-- .wpml-section-content -->

	</div> <!-- wpml-section -->
<?php
}

