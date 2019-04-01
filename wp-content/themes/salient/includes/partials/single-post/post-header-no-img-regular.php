<?php
/**
 * Post single no header BG image supplied - regular template
 *
 * @package Salient WordPress Theme
 * @subpackage Partials
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$options = get_nectar_theme_options();

$bg                                = get_post_meta( $post->ID, '_nectar_header_bg', true );
$bg_color                          = get_post_meta( $post->ID, '_nectar_header_bg_color', true );
$single_post_header_inherit_fi     = ( ! empty( $options['blog_post_header_inherit_featured_image'] ) ) ? $options['blog_post_header_inherit_featured_image'] : '0';
$theme_skin                        = ( ! empty( $options['theme-skin'] ) ) ? $options['theme-skin'] : 'original';
$header_format                     = (!empty($options['header_format'])) ? $options['header_format'] : 'default';
if($header_format == 'centered-menu-bottom-bar') {
  $theme_skin = 'material';
}
$fullscreen_header                 = ( ! empty( $options['blog_header_type'] ) && $options['blog_header_type'] == 'fullscreen' && is_singular( 'post' ) ) ? true : false;
$blog_header_type                  = ( ! empty( $options['blog_header_type'] ) ) ? $options['blog_header_type'] : 'default';
$fullscreen_class                  = ( $fullscreen_header == true ) ? 'fullscreen-header full-width-content' : null;
$blog_social_style                 = ( ! empty( $options['blog_social_style'] ) ) ? $options['blog_social_style'] : 'default';
$remove_single_post_date           = ( ! empty( $options['blog_remove_single_date'] ) ) ? $options['blog_remove_single_date'] : '0';
$remove_single_post_author         = ( ! empty( $options['blog_remove_single_author'] ) ) ? $options['blog_remove_single_author'] : '0';
$remove_single_post_comment_number = ( ! empty( $options['blog_remove_single_comment_number'] ) ) ? $options['blog_remove_single_comment_number'] : '0';
$remove_single_post_nectar_love    = ( ! empty( $options['blog_remove_single_nectar_love'] ) ) ? $options['blog_remove_single_nectar_love'] : '0';


if ( get_post_format() != 'quote' && get_post_format() != 'status' && get_post_format() != 'aside' ) {

	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();

			if ( ( empty( $bg ) && empty( $bg_color ) ) && $fullscreen_header != true && $single_post_header_inherit_fi != '1' ) { ?>

	  <div class="row heading-title hentry" data-header-style="<?php echo esc_attr( $blog_header_type ); ?>">
		<div class="col span_12 section-title blog-title">
				<?php if ( $blog_header_type == 'default_minimal' && 'post' == get_post_type() ) { ?> 
		  <span class="meta-category">

					<?php
					$categories = get_the_category();
					if ( ! empty( $categories ) ) {
						$output = null;
						foreach ( $categories as $category ) {
							$output .= '<a class="' . esc_attr( $category->slug ) . '" href="' . esc_url( get_category_link( $category->term_id ) ) . '" alt="' . esc_attr( sprintf( __( 'View all posts in %s', 'salient' ), $category->name ) ) . '">' . esc_html( $category->name ) . '</a>';
						}
						echo trim( $output ); // WPCS: XSS ok.
					}
					?>
			  </span> 

		  </span> <?php } ?>
		  <h1 class="entry-title"><?php the_title(); ?></h1>
		   
				<?php if ( 'post' == get_post_type() ) { ?>
			<div id="single-below-header">
			  <span class="meta-author vcard author"><span class="fn"><?php echo esc_html__( 'By', 'salient' ); ?> <?php the_author_posts_link(); ?></span></span>
					<?php
					$nectar_u_time          = get_the_time( 'U' );
					$nectar_u_modified_time = get_the_modified_time( 'U' );
					if ( $nectar_u_modified_time >= $nectar_u_time + 86400 ) {
					?>
				  <span class="meta-date date published"><?php echo get_the_date(); ?></span>
				  <span class="meta-date date updated rich-snippet-hidden"><?php echo get_the_modified_time( 'F jS, Y' ); ?></span>
					<?php } else { ?>
				  <span class="meta-date date updated"><?php echo get_the_date(); ?></span>
				  <?php } 
					if ( $blog_header_type != 'default_minimal' ) { ?>
				  <span class="meta-category"><?php the_category( ', ' ); ?></span> 
				<?php } else { ?>
				  <span class="meta-comment-count"><a href="<?php comments_link(); ?>"> <?php comments_number( esc_html__( 'No Comments', 'salient' ), esc_html__( 'One Comment ', 'salient' ), esc_html__( '% Comments', 'salient' ) ); ?></a></span>
				<?php } ?>
			</div><!--/single-below-header-->
		<?php } 
		   
			if ( $blog_header_type != 'default_minimal' && 'post' == get_post_type() ) { ?>
			<div id="single-meta" data-sharing="<?php echo ( ! empty( $options['blog_social'] ) && $options['blog_social'] == 1 ) ? '1' : '0'; ?>">
			  <ul>
				
				<li class="meta-comment-count">
				  <a href="<?php comments_link(); ?>"><i class="icon-default-style steadysets-icon-chat"></i> <?php comments_number( esc_html__( 'No Comments', 'salient' ), esc_html__( 'One Comment ', 'salient' ), esc_html__( '% Comments', 'salient' ) ); ?></a>
				</li>
				
					<?php if ( $remove_single_post_nectar_love != '1' ) { ?>
				 <li><?php echo '<span class="n-shortcode">' . nectar_love( 'return' ) . '</span>'; ?></li>
					<?php } 

					
					if ( ! empty( $options['blog_social'] ) && $options['blog_social'] == 1 && $blog_social_style != 'fixed_bottom_right' ) {

						echo '<li class="meta-share-count"><a href="#"><i class="icon-default-style steadysets-icon-share"></i><span class="share-count-total">0</span></a> <div class="nectar-social">';

						// facebook
						if ( ! empty( $options['blog-facebook-sharing'] ) && $options['blog-facebook-sharing'] == 1 ) {
							echo "<a class='facebook-share nectar-sharing' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-facebook'></i> <span class='count'></span></a>";
						}
						// twitter
						if ( ! empty( $options['blog-twitter-sharing'] ) && $options['blog-twitter-sharing'] == 1 ) {
							echo "<a class='twitter-share nectar-sharing' href='#' title='" . esc_attr__( 'Tweet this', 'salient' ) . "'> <i class='fa fa-twitter'></i> <span class='count'></span></a>";
						}
						// google plus
						if ( ! empty( $options['blog-google-plus-sharing'] ) && $options['blog-google-plus-sharing'] == 1 ) {
							echo "<a class='google-plus-share nectar-sharing-alt' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-google-plus'></i> <span class='count'>0</span></a>";
						}

						// linkedIn
						if ( ! empty( $options['blog-linkedin-sharing'] ) && $options['blog-linkedin-sharing'] == 1 ) {
							echo "<a class='linkedin-share nectar-sharing' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-linkedin'></i> <span class='count'> </span></a>";
						}
						// pinterest
						if ( ! empty( $options['blog-pinterest-sharing'] ) && $options['blog-pinterest-sharing'] == 1 ) {
							echo "<a class='pinterest-share nectar-sharing' href='#' title='" . esc_attr__( 'Pin this', 'salient' ) . "'> <i class='fa fa-pinterest'></i> <span class='count'></span></a>";
						}

						echo '</div></li>';

					}
					?>

				
  
			  </ul>

			</div><!--/single-meta-->

			<?php } ?>
		</div><!--/section-title-->
	  </div><!--/row-->
	
	<?php }

endwhile;
endif;

} ?>