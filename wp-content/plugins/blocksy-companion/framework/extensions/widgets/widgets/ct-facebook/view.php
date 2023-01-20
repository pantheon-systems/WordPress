<?php
/**
 * Facebook widget
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package Blocksy
 */


// Widget title
$title = blocksy_default_akg( 'title', $atts, __( 'Facebook', 'blocksy-companion' ) );

$url = blocksy_default_akg('facebook_page_url', $atts, '');

if (empty($url)) {
	return;
}

$faces = blocksy_default_akg('facebook_faces', $atts, 'yes') === 'yes' ? 'true' : 'false';
$cover = blocksy_default_akg('facebook_cover', $atts, 'no') === 'yes' ? 'false' : 'true';
$header = blocksy_default_akg('facebook_small_header', $atts, 'no') === 'yes' ? 'true' : 'false';
$timeline = blocksy_default_akg('facebook_timeline', $atts, 'no') === 'yes' ? 'timeline' : '';
$posts = blocksy_default_akg('facebook_posts', $atts, 'no') === 'yes' ? 'true' : 'false';

$app_id = '';

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $before_widget;

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $before_title . wp_kses_post( $title ) . $after_title;

?>

<div class="ct-facebook-like-box">
	<div class="fb-page"
		data-href="<?php echo esc_url( $url ); ?>"
		data-width="500"
		data-hide-cover="<?php echo $cover; ?>"
		data-show-facepile="<?php echo $faces; ?>"
		data-small-header="<?php echo $header; ?>"
		data-tabs="<?php echo $timeline; ?>"></div>

	<div id="fb-root"></div>

	<script>(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = 'https://connect.facebook.net/<?php echo esc_attr(get_locale()); ?>/sdk.js#xfbml=1&version=v3.2<?php
		if ( ! empty( $app_id ) ) {
			echo '&appId=' . esc_attr($app_id);
		}
		?>';
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>

</div>

<?php echo wp_kses_post( $after_widget ); ?>
