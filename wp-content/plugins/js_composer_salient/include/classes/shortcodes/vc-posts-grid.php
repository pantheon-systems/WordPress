<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class WPBakeryShortCode_VC_Posts_Grid extends WPBakeryShortCode {
	public $pretty_rel_random;
	protected $filter_categories = array();
	protected $query = false;
	protected $loop_args = array();
	protected $taxonomies = false;
	protected $partial_paths = array();
	protected static $pretty_photo_loaded = false;
	protected $teaser_data = false;
	public $link_target;
	protected $block_template_dir_name = 'post_block';
	protected $block_template_filename = '_item.php';
	protected static $meta_data_name = 'vc_teaser';

	function __construct( $settings ) {
		parent::__construct( $settings );
	}

	/**
	 * Get teaser box data from database.
	 *
	 * @param $name
	 * @param bool $id
	 *
	 * @return string
	 */
	public function getTeaserData( $name, $id = false ) {
		if ( false === $id ) {
			$id = get_the_ID();
		}
		$this->teaser_data = get_post_meta( $id, self::$meta_data_name, true );

		return isset( $this->teaser_data[ $name ] ) ? $this->teaser_data[ $name ] : '';
	}

	protected function getCategoriesCss( $post_id ) {
		$categories_css = '';
		$post_categories = wp_get_object_terms( $post_id, $this->getTaxonomies() );
		foreach ( $post_categories as $cat ) {
			if ( ! in_array( $cat->term_id, $this->filter_categories ) ) {
				$this->filter_categories[] = $cat->term_id;
			}
			$categories_css .= ' grid-cat-' . $cat->term_id;
		}

		return $categories_css;
	}

	protected function resetTaxonomies() {
		$this->taxonomies = false;
	}

	protected function getTaxonomies() {
		if ( false === $this->taxonomies ) {
			$this->taxonomies = get_object_taxonomies( ! empty( $this->loop_args['post_type'] ) ? $this->loop_args['post_type'] : get_post_types( array(
				'public' => false,
				'name' => 'attachment',
			), 'names', 'NOT' ) );
		}

		return $this->taxonomies;
	}

	protected function getLoop( $loop ) {
		global $vc_posts_grid_exclude_id;
		$vc_posts_grid_exclude_id[] = get_the_ID();
		require_once vc_path_dir( 'PARAMS_DIR', 'loop/loop.php' );
		list( $this->loop_args, $this->query ) = vc_build_loop_query( $loop, $vc_posts_grid_exclude_id );
	}

	protected function spanClass( $grid_columns_count ) {
		$teaser_width = '';
		switch ( $grid_columns_count ) {
			case '1' :
				$teaser_width = 'vc_col-sm-12';
				break;
			case '2' :
				$teaser_width = 'vc_col-sm-6';
				break;
			case '3' :
				$teaser_width = 'vc_col-sm-4';
				break;
			case '4' :
				$teaser_width = 'vc_col-sm-3';
				break;
			case '5':
				$teaser_width = 'vc_col-sm-10';
				break;
			case '6' :
				$teaser_width = 'vc_col-sm-2';
				break;
		}

		return $teaser_width;
	}

	protected function getMainCssClass( $filter ) {
		return 'wpb_' . ( 'yes' === $filter ? 'filtered_' : '' ) . 'grid';
	}

	protected function getFilterCategories() {
		return get_terms( $this->getTaxonomies(), array(
			'orderby' => 'name',
			'include' => implode( ',', $this->filter_categories ),
		) );
	}

	protected function getPostThumbnail( $post_id, $grid_thumb_size ) {
		return wpb_getImageBySize( array( 'post_id' => $post_id, 'thumb_size' => $grid_thumb_size ) );
	}

	protected function getPostContent() {
		remove_filter( 'the_content', 'wpautop' );
		$content = str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', get_the_content() ) );

		return $content;
	}

	protected function getPostExcerpt() {
		remove_filter( 'the_excerpt', 'wpautop' );
		$content = apply_filters( 'the_excerpt', get_the_excerpt() );

		return $content;
	}

	protected function getLinked( $post, $content, $type, $css_class ) {
		$output = '';
		if ( 'link_post' === $type || empty( $type ) ) {
			$url = get_permalink( $post->id );
			$title = sprintf( esc_attr__( 'Permalink to %s', 'js_composer' ), $post->title_attribute );
			$output .= '<a href="' . $url . '" class="' . $css_class . '"' . $this->link_target . ' title="' . $title . '">' . $content . '</a>';
		} elseif ( 'link_image' === $type && isset( $post->image_link ) && ! empty( $post->image_link ) ) {
			$this->loadPrettyPhoto();
			// actually fixes relations if more prettyphoto added on page
			if ( ! $this->pretty_rel_random ) {
				$this->pretty_rel_random = ' data-rel="prettyPhoto[rel-' . get_the_ID() . '-' . rand() . ']"';
			}
			$output .= '<a href="' . $post->image_link . '" class="' . $css_class . ' prettyphoto"' . $this->pretty_rel_random . ' ' . $this->link_target . ' title="' . $post->title_attribute . '">' . $content . '</a>';
		} else {
			$output .= $content;
		}

		return $output;
	}

	protected function loadPrettyPhoto() {
		if ( true !== self::$pretty_photo_loaded ) {
			wp_enqueue_script( 'prettyphoto' );
			wp_enqueue_style( 'prettyphoto' );
			self::$pretty_photo_loaded = true;
		}
	}

	protected function setLinkTarget( $grid_link_target = '' ) {
		$this->link_target = '_blank' === $grid_link_target ? ' target="_blank"' : '';
	}

	protected function findBlockTemplate() {
		$template_path = $this->block_template_dir_name . '/' . $this->block_template_filename;
		// Check template path in shortcode's mapping settings
		if ( ! empty( $this->settings['html_template'] ) && is_file( $this->settings( 'html_template' ) . $template_path ) ) {
			return $this->settings['html_template'] . $template_path;
		}
		// Check template in theme directory
		$user_template = vc_shortcodes_theme_templates_dir( $template_path );

		if ( is_file( $user_template ) ) {
			return $user_template;
		}
		// Check default place
		$default_dir = vc_manager()->getDefaultShortcodesTemplatesDir() . '/';
		if ( is_file( $default_dir . $template_path ) ) {
			return $default_dir . $template_path;
		}

		return $template_path;
	}

	protected function getBlockTemplate() {
		if ( ! isset( $this->block_template_path ) ) {
			$this->block_template_path = $this->findBlockTemplate();
		}

		return $this->block_template_path;
	}
}
