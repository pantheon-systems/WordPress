<?php
/**
 * Portfolio single sidebar
 *
 * @package Salient WordPress Theme
 * @subpackage Partials
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$options = get_nectar_theme_options();

$project_social_style = ( ! empty( $options['portfolio_social_style'] ) ) ? $options['portfolio_social_style'] : 'default';

global $post;

?>

<div id="sidebar" class="col span_3 col_last" data-follow-on-scroll="<?php echo ( ! empty( $options['portfolio_sidebar_follow'] ) && $options['portfolio_sidebar_follow'] == 1 ) ? 1 : 0; ?>">
		
  <div id="sidebar-inner">
	
	<div id="project-meta" data-sharing="<?php echo ( ! empty( $options['portfolio_social'] ) && $options['portfolio_social'] == 1 ) ? '1' : '0'; ?>">

		
		<ul class="project-sharing"> 


		<?php
		// portfolio social sharting icons
		if ( ! empty( $options['portfolio_social'] ) && $options['portfolio_social'] == 1 && $project_social_style != 'fixed_bottom_right' ) {

			echo '<li class="meta-share-count"><a href="#"><i class="icon-default-style steadysets-icon-share"></i><span class="share-count-total">0</span> <span class="plural">' . esc_html__( 'Shares', 'salient' ) . '</span> <span class="singular">' . esc_html__( 'Share', 'salient' ) . '</span></a> <div class="nectar-social">';


			// facebook
			if ( ! empty( $options['portfolio-facebook-sharing'] ) && $options['portfolio-facebook-sharing'] == 1 ) {
				echo "<a class='facebook-share nectar-sharing' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-facebook'></i> <span class='count'></span></a>";
			}
			// twitter
			if ( ! empty( $options['portfolio-twitter-sharing'] ) && $options['portfolio-twitter-sharing'] == 1 ) {
				echo "<a class='twitter-share nectar-sharing' href='#' title='" . esc_attr__( 'Tweet this', 'salient' ) . "'> <i class='fa fa-twitter'></i> <span class='count'></span></a>";
			}
			// google plus
			if ( ! empty( $options['portfolio-google-plus-sharing'] ) && $options['portfolio-google-plus-sharing'] == 1 ) {
				echo "<a class='google-plus-share nectar-sharing-alt' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-google-plus'></i> <span class='count'>0</span></a>";
			}

			// linkedIn
			if ( ! empty( $options['portfolio-linkedin-sharing'] ) && $options['portfolio-linkedin-sharing'] == 1 ) {
				echo "<a class='linkedin-share nectar-sharing' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-linkedin'></i> <span class='count'> </span></a>";
			}
			// pinterest
			if ( ! empty( $options['portfolio-pinterest-sharing'] ) && $options['portfolio-pinterest-sharing'] == 1 ) {
				echo "<a class='pinterest-share nectar-sharing' href='#' title='" . esc_attr__( 'Pin this', 'salient' ) . "'> <i class='fa fa-pinterest'></i> <span class='count'></span></a>";
			}

			echo '</div></li>';

		}

		echo '<li><span class="n-shortcode">' . nectar_love( 'return' ) . '</span></li>';


		if ( ! empty( $options['portfolio_date'] ) && $options['portfolio_date'] == 1 ) {
			if ( empty( $options['portfolio_social'] ) || $options['portfolio_social'] == 0 || $project_social_style == 'fixed_bottom_right' ) {
				?>

			<li class="project-date">
				<?php the_time( 'F d, Y' ); ?>
			</li>
				<?php
			}
		}
		?>
	  </ul><!--sharing-->

	  <div class="clear"></div>
	</div><!--project-meta-->
	
	<?php
	
	$nectar_using_VC_front_end_editor = ( isset($_GET['vc_editable']) ) ? sanitize_text_field($_GET['vc_editable']) : '';
	$nectar_using_VC_front_end_editor = ($nectar_using_VC_front_end_editor == 'true') ? true : false;
	
	if($nectar_using_VC_front_end_editor) {
		
		$fe_editor_sidebar_content = $post->post_content;
		
		if( function_exists( 'wptexturize' ) ) {
			$fe_editor_sidebar_content = wptexturize($fe_editor_sidebar_content);
		}
		if( function_exists( 'convert_smilies' ) ) {
			$fe_editor_sidebar_content = convert_smilies($fe_editor_sidebar_content);
		}
		if( function_exists( 'convert_chars' ) ) {
			$fe_editor_sidebar_content = convert_chars($fe_editor_sidebar_content);
		}
		if( function_exists( 'wpautop' ) ) {
			$fe_editor_sidebar_content = wpautop($fe_editor_sidebar_content);
		}
		if( function_exists( 'shortcode_unautop' ) ) {
			$fe_editor_sidebar_content = shortcode_unautop($fe_editor_sidebar_content);
		}
		
		echo wp_kses_post( $fe_editor_sidebar_content );
		
	} else {
		the_content();
	}

	
	
	$project_attrs = get_the_terms( $post->ID, 'project-attributes' );
	if ( ! empty( $project_attrs ) ) {
		?>
	  <ul class="project-attrs checks">
		<?php
		foreach ( $project_attrs as $attr ) {
			echo '<li>' . wp_kses_post( $attr->name ) . '</li>';
		}
		?>
	  </ul>
	<?php } ?>
  

  </div>
  
</div><!--/sidebar-->