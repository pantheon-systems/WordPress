<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Vendor_YoastSeo
 * @since 4.4
 */
class Vc_Vendor_YoastSeo implements Vc_Vendor_Interface {

	/**
	 * Created to improve yoast multiply calling wpseo_pre_analysis_post_content filter.
	 * @since 4.5.3
	 * @var string - parsed post content
	 */
	protected $parsedContent;

	function __construct() {
		add_action( 'vc_backend_editor_render', array(
			$this,
			'enqueueJs',
		) );
		add_filter( 'wpseo_sitemap_urlimages', array(
			$this,
			'filterSitemapUrlImages',
		), 10, 2 );
	}

	/**
	 * Add filter for yoast.
	 * @since 4.4
	 */
	public function load() {
		if ( class_exists( 'WPSEO_Metabox' ) && ( 'admin_page' === vc_mode() || 'admin_frontend_editor' === vc_mode() ) ) {
			add_filter( 'wpseo_pre_analysis_post_content', array(
				$this,
				'filterResults',
			) );
		}
	}

	/**
	 * Properly parse content to detect images/text keywords.
	 * @since 4.4
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function filterResults( $content ) {
		if ( empty( $this->parsedContent ) ) {
			global $post, $wp_the_query;
			$wp_the_query->post = $post; // since 4.5.3 to avoid the_post replaces
			/**
			 * @since 4.4.3
			 * vc_filter: vc_vendor_yoastseo_filter_results
			 */
			do_action( 'vc_vendor_yoastseo_filter_results' );
			$this->parsedContent = do_shortcode( shortcode_unautop( $content ) );
			wp_reset_query();
		}

		return $this->parsedContent;
	}

	/**
	 * @since 4.4
	 */
	public function enqueueJs() {
		require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/editor/class-vc-grid-item-editor.php' );
		if ( get_post_type() === Vc_Grid_Item_Editor::postType() ) {
			return;
		}
		wp_enqueue_script( 'vc_vendor_yoast_js', vc_asset_url( 'js/vendors/yoast.js' ), array( 'yoast-seo-post-scraper' ), WPB_VC_VERSION, true );
	}

	public function frontendEditorBuild() {
		$vc_yoast_meta_box = $GLOBALS['wpseo_metabox'];
		remove_action( 'admin_init', array(
			$GLOBALS['wpseo_meta_columns'],
			'setup_hooks',
		) );
		apply_filters( 'wpseo_use_page_analysis', false );
		remove_action( 'add_meta_boxes', array(
			$vc_yoast_meta_box,
			'add_meta_box',
		) );
		remove_action( 'admin_enqueue_scripts', array(
			$vc_yoast_meta_box,
			'enqueue',
		) );
		remove_action( 'wp_insert_post', array(
			$vc_yoast_meta_box,
			'save_postdata',
		) );
		remove_action( 'edit_attachment', array(
			$vc_yoast_meta_box,
			'save_postdata',
		) );
		remove_action( 'add_attachment', array(
			$vc_yoast_meta_box,
			'save_postdata',
		) );
		remove_action( 'post_submitbox_start', array(
			$vc_yoast_meta_box,
			'publish_box',
		) );
		remove_action( 'admin_init', array(
			$vc_yoast_meta_box,
			'setup_page_analysis',
		) );
		remove_action( 'admin_init', array(
			$vc_yoast_meta_box,
			'translate_meta_boxes',
		) );
		remove_action( 'admin_footer', array(
			$vc_yoast_meta_box,
			'template_keyword_tab',
		) );
	}

	public function filterSitemapUrlImages( $images, $id ) {
		if ( empty( $images ) ) {
			$post = get_post( $id );
			if ( $post && strpos( $post->post_content, '[vc_row' ) !== false ) {
				preg_match_all( '/(?:image|images|ids|include)\=\"([^\"]+)\"/', $post->post_content, $matches );
				foreach ( $matches[1] as $m ) {
					$ids = explode( ',', $m );
					foreach ( $ids as $id ) {
						if ( (int) $id ) {
							$images[] = array(
								'src' => wp_get_attachment_url( $id ),
								'title' => get_the_title( $id ),
							);
						}
					}
				}
			}
		}

		return $images;
	}
}
