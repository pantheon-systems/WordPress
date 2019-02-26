<?php
/**
 * Post single no header BG image supplied - fullscreen template
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
$header_format = (!empty($options['header_format'])) ? $options['header_format'] : 'default';
if($header_format == 'centered-menu-bottom-bar') {
  $theme_skin = 'material';
}	
$fullscreen_header                 = ( ! empty( $options['blog_header_type'] ) && $options['blog_header_type'] == 'fullscreen' && is_singular( 'post' ) ) ? true : false;
$fullscreen_class                  = ( $fullscreen_header == true ) ? 'fullscreen-header full-width-content' : null;
$blog_social_style                 = ( ! empty( $options['blog_social_style'] ) ) ? $options['blog_social_style'] : 'default';
$remove_single_post_date           = ( ! empty( $options['blog_remove_single_date'] ) ) ? $options['blog_remove_single_date'] : '0';
$remove_single_post_author         = ( ! empty( $options['blog_remove_single_author'] ) ) ? $options['blog_remove_single_author'] : '0';
$remove_single_post_comment_number = ( ! empty( $options['blog_remove_single_comment_number'] ) ) ? $options['blog_remove_single_comment_number'] : '0';
$remove_single_post_nectar_love    = ( ! empty( $options['blog_remove_single_nectar_love'] ) ) ? $options['blog_remove_single_nectar_love'] : '0';

if ( empty( $bg ) && empty( $bg_color ) && $single_post_header_inherit_fi != '1' ) { ?>
  <div id="page-header-wrap" data-animate-in-effect="none" data-midnight="light" class="fullscreen-header">	
  <div class="not-loaded default-blog-title fullscreen-header hentry" id="page-header-bg" data-midnight="light" data-alignment-v="middle" data-alignment="center" data-parallax="0" data-height="450" data-remove-post-date="<?php echo esc_attr( $remove_single_post_date ); ?>" data-remove-post-author="<?php echo esc_attr( $remove_single_post_author ); ?>" data-remove-post-comment-number="<?php echo esc_attr( $remove_single_post_comment_number ); ?>">
	<div class="container">	
	  <div class="row">
		<div class="col span_6 section-title blog-title">
		  <?php
			if ( ( $post->post_type == 'post' && is_single() ) && $theme_skin == 'material' ) {
				$categories = get_the_category();
				if ( ! empty( $categories ) ) {
					$output = null;
					foreach ( $categories as $category ) {
						$output .= '<a class="' . esc_attr( $category->slug ) . '" href="' . esc_url( get_category_link( $category->term_id ) ) . '" alt="' . esc_attr( sprintf( __( 'View all posts in %s', 'salient' ), $category->name ) ) . '">' . esc_html( $category->name ) . '</a>';
					}
					echo trim( $output );
				}
			}
			?>
		   
		  <h1 class="entry-title"><?php the_title(); ?></h1>
		  <div class="author-section">
			<span class="meta-author">  
			  <?php
				if ( function_exists( 'get_avatar' ) ) {
					echo get_avatar( get_the_author_meta( 'email' ), 100 ); }
				?>
			</span> 
			 <div class="avatar-post-info vcard author">
			  <span class="fn"><?php the_author_posts_link(); ?></span>
			  <?php
				$nectar_u_time          = get_the_time( 'U' );
				$nectar_u_modified_time = get_the_modified_time( 'U' );
				if ( $nectar_u_modified_time >= $nectar_u_time + 86400 ) {
					?>
				  <span class="meta-date date published"><i><?php echo get_the_date(); ?></i></span>
				  <span class="meta-date date updated rich-snippet-hidden"><?php echo get_the_modified_time( 'F jS, Y' ); ?></span>
				<?php } else { ?>
				  <span class="meta-date date updated"><i><?php echo get_the_date(); ?></i></span>
				<?php } ?>
			 </div>
		  </div>
		</div>
	  </div>
	</div>
	<?php
	   $button_styling = ( ! empty( $options['button-styling'] ) ) ? $options['button-styling'] : 'default';
	if ( $button_styling == 'default' ) {
		echo '<div class="scroll-down-wrap"><a href="#" class="section-down-arrow"><i class="icon-salient-down-arrow icon-default-style"> </i></a></div>';
	} elseif ( $button_styling == 'slightly_rounded' || $button_styling == 'slightly_rounded_shadow' ) {
		echo '<div class="scroll-down-wrap no-border"><a href="#" class="section-down-arrow"><svg class="nectar-scroll-icon" viewBox="0 0 30 45" enable-background="new 0 0 30 45">
                    <path class="nectar-scroll-icon-path" fill="none" stroke="#ffffff" stroke-width="2" stroke-miterlimit="10" d="M15,1.118c12.352,0,13.967,12.88,13.967,12.88v18.76  c0,0-1.514,11.204-13.967,11.204S0.931,32.966,0.931,32.966V14.05C0.931,14.05,2.648,1.118,15,1.118z"></path>
                  </svg></a></div>';
	} else {
		echo '<div class="scroll-down-wrap"><a href="#" class="section-down-arrow"><i class="fa fa-angle-down top"></i><i class="fa fa-angle-down"></i></a></div>';
	}
	?>
  </div>
  </div>
	<?php
}


if ( $theme_skin != 'ascend' && $theme_skin != 'material' ) {
	?>
  <div class="container">
	<div id="single-below-header" class="<?php echo esc_attr( $fullscreen_class ); ?> custom-skip" data-remove-post-comment-number="<?php echo esc_attr( $remove_single_post_comment_number ); ?>">
	  <?php if ( $blog_social_style != 'fixed_bottom_right' ) { ?>
		<span class="meta-share-count"><i class="icon-default-style steadysets-icon-share"></i> 
			<?php
			echo '<a href=""><span class="share-count-total">0</span> <span class="plural">' . esc_html__( 'Shares', 'salient' ) . '</span> <span class="singular">' . esc_html__( 'Share', 'salient' ) . '</span> </a>';
			nectar_blog_social_sharing();
			?>
		 </span>
		<?php } else { ?>
		  <?php if ( $remove_single_post_nectar_love != '1' ) { ?>
			  <span class="meta-love"><span class="n-shortcode"> <?php echo nectar_love( 'return' ); ?>  </span></span>
			<?php } ?>
		<?php } ?>
	  <span class="meta-category"><i class="icon-default-style steadysets-icon-book2"></i> <?php the_category( ', ' ); ?></span>
	  <span class="meta-comment-count"><i class="icon-default-style steadysets-icon-chat-3"></i> <a href="<?php comments_link(); ?>"><?php comments_number( esc_html__( 'No Comments', 'salient' ), esc_html__( 'One Comment ', 'salient' ), esc_html__( '% Comments', 'salient' ) ); ?></a></span>
	</div><!--/single-below-header-->
  </div>

	<?php
}
