<?php

/**
 * Displays the main Better Search Replace page under Tools -> Better Search Replace.
 *
 * @link       https://bettersearchreplace.com
 * @since      1.0.0
 *
 * @package    Better_Search_Replace
 * @subpackage Better_Search_Replace/templates
 */

// Prevent direct access.
if ( ! defined( 'BSR_PATH' ) ) exit;

// Determines which tab to display.
$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'bsr_search_replace';

switch( $active_tab ) {
	case 'bsr_settings':
		$action = 'action="' . get_admin_url() . 'options.php' . '"';
		break;
	case 'bsr_help':
		$action = 'action="' . get_admin_url() . 'admin-post.php' . '"';
		break;
	default:
		$action = '';
}

?>

<div class="wrap">

	<h1 id="bsr-title"><?php _e( 'Better Search Replace', 'better-search-replace' ); ?></h1>
	<?php settings_errors(); ?>

	<div id="bsr-error-wrap"></div>

	<?php BSR_Admin::render_result(); ?>

	<div id="bsr-main">

		<div id="bsr-tabs">

			<h2 id="bsr-nav-tab-wrapper" class="nav-tab-wrapper">
			    <a href="?page=better-search-replace&tab=bsr_search_replace" class="nav-tab <?php echo $active_tab == 'bsr_search_replace' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Search/Replace', 'better-search-replace' ); ?></a>
			    <a href="?page=better-search-replace&tab=bsr_settings" class="nav-tab <?php echo $active_tab == 'bsr_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Settings', 'better-search-replace' ); ?></a>
			    <a href="?page=better-search-replace&tab=bsr_help" class="nav-tab <?php echo $active_tab == 'bsr_help' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Help', 'better-search-replace' ); ?></a>
			</h2>

			<form class="bsr-action-form" <?php echo $action; ?> method="POST">

			<?php
				// Include the correct tab template.
				$bsr_template = str_replace( '_', '-', $active_tab ) . '.php';
				if ( file_exists( BSR_PATH . 'templates/' . $bsr_template ) ) {
					include BSR_PATH . 'templates/' . $bsr_template;
				} else {
					include BSR_PATH . 'templates/bsr-search-replace.php';
				}
			?>

			</form>

		</div><!-- /#bsr-tabs -->

		<div id="bsr-sidebar-wrap">

			<div id="bsr-upgrade">

				<a href="https://bettersearchreplace.com/?utm_source=insideplugin&utm_medium=web&utm_content=sidebar&utm_campaign=pro-upsell">

					<img src="<?php echo BSR_URL; ?>/assets/img/bsr-logo-white.svg" />
					<h1><?php _e( 'Upgrade to Pro', 'better-search-replace' ); ?></h1>

					<ul>
						<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Backup to an SQL file', 'better-search-replace' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Import an SQL file and run a find/replace on it', 'better-search-replace' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Detailed report of exactly what was replaced', 'better-search-replace' ); ?></li>
					</ul>

				</a>

			</div>

			<form id="bsr-upgrade-form" method="post" action="https://deliciousbrains.com/email-subscribe/" target="_blank" class="subscribe block">
				<h1><?php _e( '20% Off!', 'better-search-replace' ); ?></h1>

				<?php $user = wp_get_current_user(); ?>

				<p class="interesting">
					<?php echo wptexturize( __( "Submit your name and email and we'll send you a coupon for 20% off your upgrade to the pro version.", 'better-search-replace' ) ); ?>
				</p>

				<div class="field">
					<input type="email" name="email" value="<?php echo esc_attr( $user->user_email ); ?>" placeholder="<?php _e( 'Your Email', 'better-search-replace' ); ?>"/>
				</div>

				<div class="field">
					<input type="text" name="first_name" value="<?php echo esc_attr( trim( $user->first_name ) ); ?>" placeholder="<?php _e( 'First Name', 'better-search-replace' ); ?>"/>
				</div>

				<div class="field">
					<input type="text" name="last_name" value="<?php echo esc_attr( trim( $user->last_name ) ); ?>" placeholder="<?php _e( 'Last Name', 'better-search-replace' ); ?>"/>
				</div>

				<input type="hidden" name="campaigns[]" value="9" />
				<input type="hidden" name="source" value="10" />

				<div class="field submit-button">
					<input type="submit" class="button" value="<?php _e( 'Send me the coupon', 'better-search-replace' ); ?>"/>
				</div>

				<p class="promise">
					<?php _e( 'You\'ll also receive our awesome weekly posts from the Delicious Brains blog. Unsubscribe anytime.', 'better-search-replace' ); ?>
				</p>
			</form>

			<div id="bsr-review">
				<div class="bsr-stars">
					<span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span>
				</div>
				<div class="bsr-review-details">
					<h3>"Worked Beautifully"</h3>
					<img class="bsr-review-avatar" src="<?php echo BSR_URL . 'assets/img/phil-gravatar.jpeg'; ?>" />
					<a href="https://wordpress.org/support/topic/worked-beautifully-10/"><span class="bsr-review-author">@philraymond<br><?php _e( 'via WordPress.org', 'better-search-replace' ); ?></span></a>
				</div>
			</div>

		</div><!-- /#bsr-sidebar-wrap -->

	</div><!-- /#bsr-main -->

</div><!-- /.wrap -->
