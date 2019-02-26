<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $grid_columns_count
 * @var $grid_teasers_count
 * @var $grid_layout
 * @var $grid_link_target
 * @var $filter
 * @var $grid_thumb_size
 * @var $grid_layout_mode
 * @var $el_class
 * @var $loop
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Posts_Grid
 */
$title = $grid_columns_count = $grid_teasers_count = $grid_layout =
$grid_link_target = $filter = $grid_thumb_size = $grid_layout_mode = $el_class = $loop = '';

global $vc_teaser_box;
$grid_link = '';
$posts = array();
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$this->resetTaxonomies();
if ( empty( $loop ) ) {
	return;
}
$this->getLoop( $loop );
$my_query = $this->query;
$args = $this->loop_args;
$teaser_blocks = vc_sorted_list_parse_value( $grid_layout );
global $vc_posts_grid_exclude_id;
/** @var $my_query WP_Query */
while ( $my_query->have_posts() ) {
	$my_query->the_post(); // Get post from query
	$post = new stdClass(); // Creating post object.
	if ( in_array( get_the_ID(), $vc_posts_grid_exclude_id ) ) {
		continue;
	}
	$post->id = get_the_ID();
	$post->link = get_permalink( $post->id );
	if ( '1' === $vc_teaser_box->getTeaserData( 'enable', $post->id ) ) {
		$post->custom_user_teaser = true;
		$data = $vc_teaser_box->getTeaserData( 'data', $post->id );
		if ( ! empty( $data ) ) {
			$data = json_decode( $data );
		}
		$post->bgcolor = $vc_teaser_box->getTeaserData( 'bgcolor', $post->id );
		$post->custom_teaser_blocks = array();
		$post->title_attribute = the_title_attribute( 'echo=0' );
		if ( ! empty( $data ) ) {
			foreach ( $data as $block ) {
				$settings = array();
				if ( 'title' === $block->name ) {
					$post->title = the_title( '', '', false );
				} elseif ( 'image' === $block->name ) {
					if ( 'featured' === $block->image ) {
						$post->thumbnail_data = $this->getPostThumbnail( $post->id, $grid_thumb_size );
					} elseif ( ! empty( $block->image ) ) {
						$post->thumbnail_data = wpb_getImageBySize( array(
							'attach_id' => (int) $block->image,
							'thumb_size' => $grid_thumb_size,
						) );
					} else {
						$post->thumbnail_data = false;
					}
					$post->thumbnail = $post->thumbnail_data && isset( $post->thumbnail_data['thumbnail'] ) ? $post->thumbnail_data['thumbnail'] : '';
					$post->image_link = empty( $video ) && $post->thumbnail && isset( $post->thumbnail_data['p_img_large'][0] ) ? $post->thumbnail_data['p_img_large'][0] : $video;
				} elseif ( 'text' === $block->name ) {
					if ( 'custom' === $block->mode ) {
						$settings[] = 'text';
						$post->content = $block->text;
					} elseif ( 'excerpt' === $block->mode ) {
						$settings[] = $block->mode;
						$post->excerpt = $this->getPostExcerpt();
					} else {
						$settings[] = $block->mode;
						$post->content = $this->getPostContent();
					}
				}
				if ( isset( $block->link ) ) {
					if ( 'post' === $block->link ) {
						$settings[] = 'link_post';
					} elseif ( 'big_image' === $block->link ) {
						$settings[] = 'link_image';
					} else {
						$settings[] = 'no_link';
					}
					$settings[] = '';
				}
				$post->custom_teaser_blocks[] = array( $block->name, $settings );
			}
		}
	} else {
		$post->custom_user_teaser = false;
		$post->title = the_title( '', '', false );
		$post->title_attribute = the_title_attribute( 'echo=0' );
		$post->post_type = get_post_type();
		$post->content = $this->getPostContent();
		$post->excerpt = $this->getPostExcerpt();
		$post->thumbnail_data = $this->getPostThumbnail( $post->id, $grid_thumb_size );
		$post->thumbnail = $post->thumbnail_data && isset( $post->thumbnail_data['thumbnail'] ) ? $post->thumbnail_data['thumbnail'] : '';
		$video = get_post_meta( $post->id, '_p_video', true );
		$post->image_link = empty( $video ) && $post->thumbnail && isset( $post->thumbnail_data['p_img_large'][0] ) ? $post->thumbnail_data['p_img_large'][0] : $video;
	}

	$post->categories_css = $this->getCategoriesCss( $post->id );

	$posts[] = $post;
}
wp_reset_query();

/**
 * Css classes for grid and teasers.
 * {{
 */
$post_types_teasers = '';
if ( ! empty( $args['post_type'] ) && is_array( $args['post_type'] ) ) {
	foreach ( $args['post_type'] as $post_type ) {
		$post_types_teasers .= 'wpb_teaser_grid_' . $post_type . ' ';
	}
}
$li_span_class = $this->spanClass( $grid_columns_count );

$css_class = 'wpb_row wpb_teaser_grid wpb_content_element ' .
			 $this->getMainCssClass( $filter ) . // Css class as selector for isotope plugin
			 ' columns_count_' . $grid_columns_count . // Custom margin/padding for different count of columns in grid
			 ' columns_count_' . $grid_columns_count . // Combination of layout and column count
			 ' ' . $post_types_teasers; // Css classes by selected post types

$class_to_filter = $css_class;
$class_to_filter .= $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$this->setLinkTarget( $grid_link_target );

?>
	<div class="<?php echo esc_attr( trim( $css_class ) ); ?>">
		<div class="wpb_wrapper">
			<?php echo wpb_widget_title( array( 'title' => $title, 'extraclass' => 'wpb_teaser_grid_heading' ) ) ?>
			<div class="teaser_grid_container">
				<?php if ( 'yes' === $filter && ! empty( $this->filter_categories ) ) :
					$categories_array = $this->getFilterCategories();
					echo '<ul class="categories_filter vc_col-sm-12 vc_clearfix">'
					     . '<li class="active"><a href="#" data-filter="*">';
					_e( 'All', 'js_composer' );
					echo '</a></li>';
					foreach ( $this->getFilterCategories() as $cat ) :
						echo '<li><a href="#"'
						     . ' data-filter=".grid-cat-<?php echo $cat->term_id ?>">'
						     . esc_attr( $cat->name )
						     . '</a></li>';
					endforeach;
					echo '</ul>';
					?>
					<div class="vc_clearfix"></div>
				<?php endif ?>
				<ul class="wpb_thumbnails wpb_thumbnails-fluid vc_clearfix" data-layout-mode="<?php echo $grid_layout_mode ?>">
					<?php
					/**
					 * Enqueue js/css
					 * {{
					 */
					wp_enqueue_style( 'isotope-css' );
					wp_enqueue_script( 'isotope' );
					?>
					<?php if ( count( $posts ) > 0 ) :  ?>
						<?php foreach ( $posts as $post ) :
							$blocks_to_build = true === $post->custom_user_teaser ? $post->custom_teaser_blocks : $teaser_blocks;
							$block_style = isset( $post->bgcolor ) ? ' style="background-color: ' . $post->bgcolor . '"' : '';
							echo '<li'
							     . ' class="isotope-item '
							     . esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $li_span_class, 'vc_teaser_grid_li', $atts ) )
							     . ' '
							     . $post->categories_css
							     . '"'
							     . $block_style
							     . '>';
							echo '<div class="isotope-inner">';
							foreach ( $blocks_to_build as $block_data ) :
								include $this->getBlockTemplate();
							endforeach;
							echo '</div></li>';
								?>
						<?php endforeach ?>
					<?php else : ?>
						<li class="<?php echo $this->spanClass( 1 ); ?>"><?php _e( 'Nothing found.', 'js_composer' ) ?></li>
					<?php endif ?>
				</ul>
			</div>
		</div>
		<div class="clear"></div>
	</div>
