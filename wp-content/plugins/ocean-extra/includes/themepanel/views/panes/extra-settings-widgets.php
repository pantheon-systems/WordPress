<?php

remove_filter( 'ocean_custom_widgets', array( 'Ocean_Extra_New_Theme_Panel', 'control_widgets' ), 9999 );
$widgets = apply_filters(
	'ocean_custom_widgets',
	array(
		'about-me',
		'contact-info',
		'custom-links',
		'custom-menu',
		'facebook',
		'flickr',
		'instagram',
		'mailchimp',
		'recent-posts',
		'social',
		'social-share',
		'tags',
		'twitter',
		'video',
		'custom-header-logo',
		'custom-header-nav',
	)
);

$widgets_info = array(
	'about-me'           => array(
		'title' => 'About me',
		'desc'  => 'Add image, description and social media links.',
	),
	'contact-info'       => array(
		'title' => 'Contact Info',
		'desc'  => 'Display contact info with icons.',
	),
	'custom-header-logo' => array(
		'title' => 'Custom Logo',
		'desc'  => 'Displays the logo used for Custom Header style.',
	),
	'custom-header-nav'  => array(
		'title' => 'Custom Header Nav',
		'desc'  => 'Displays the Main Menu used for Custom Header.',
	),
	'custom-links'       => array(
		'title' => 'Custom Links',
		'desc'  => 'Add up to 15 custom links.',
	),
	'custom-menu'        => array(
		'title' => 'Custom Menu',
		'desc'  => 'Display one of your menus.',
	),
	'facebook'           => array(
		'title' => 'Facebook Like Box',
		'desc'  => 'Connect visitors with your Facebook Page.',
	),
	'flickr'             => array(
		'title' => 'Flickr',
		'desc'  => 'Displays images from your Flickr account.',
	),
	'instagram'          => array(
		'title' => 'Instagram',
		'desc'  => 'Display Instagram photos.',
	),
	'mailchimp'          => array(
		'title' => 'Mailchimp',
		'desc'  => 'Displays newsletter subscription form.',
	),
	'recent-posts'       => array(
		'title' => 'Recent Posts',
		'desc'  => 'Display recent or random posts with thumbnails.',
	),
	'social'             => array(
		'title' => 'Social Icons',
		'desc'  => 'Display social media icons.',
	),
	'social-share'       => array(
		'title' => 'Social Share',
		'desc'  => 'Display social sharing buttons.',
	),
	'tags'               => array(
		'title' => 'Tags Cloud',
		'desc'  => 'Display a cloud of the most used tags.',
	),
	'twitter'            => array(
		'title' => 'Twitter',
		'desc'  => 'Display tweets from your public Twitter account.',
	),
	'video'              => array(
		'title' => 'Video',
		'desc'  => 'Display any type of videos.',
	),
);

$oe_widgets_settings = get_option( 'oe_widgets_settings', 0 );
if ( empty( $oe_widgets_settings ) && $oe_widgets_settings !== 0 ) {
	$oe_widgets_settings = array();
}
?>



<div id="ocean-widgets-control" class="column-wrap clr">
	<form class="save_panel_settings">
		<input type="hidden" name="option_name" value="oe_widgets_settings" />

		<div id="ocean-widget-disable-bulk" class="oceanwp-tp-switcher column-wrap clr">
			<label for="oceanwp-switch-widget-disable-bulk" class="column-name">
				<input type="checkbox" role="checkbox" name="widget-disable-bulk" value="true" <?php checked( ( $oe_widgets_settings === 0 ) ); ?> id="oceanwp-switch-widget-disable-bulk" class="oe-switcher-bulk" />
				<span class="slider round"></span>
			</label>
		</div>

		<div id="ocean-widget-items" class="column-wrap clr">
			<?php foreach ( $widgets as $key ) : ?>
				<?php
				$title = ucwords( str_replace( '-', ' ', $key ) );
				$desc  = '';
				if ( isset( $widgets_info[ $key ] ) ) {
					$title = ! empty( $widgets_info[ $key ]['title'] ) ? $widgets_info[ $key ]['title'] : $title;
					$desc  = ! empty( $widgets_info[ $key ]['desc'] ) ? $widgets_info[ $key ]['desc'] : '';
				}
				if ( $oe_widgets_settings === 0 ) {
					$checked_val = true;
				} else {
					$checked_val = ! empty( $oe_widgets_settings[ $key ] );
				}
				?>
				<div id="ocean-widget-<?php echo $key; ?>" class="oceanwp-tp-small-block column-wrap clr">
					<div class="oceanwp-switcher-block">
						<div class="oceanwp-switcher-item">
							<label for="oceanwp-widget-switch-[<?php echo esc_attr( $key ); ?>]" class="oceanwp-tp-switcher column-name">
								<input type="checkbox" role="checkbox" name="oe_widgets_settings[<?php echo esc_attr( $key ); ?>]" value="true" id="oceanwp-widget-switch-[<?php echo esc_attr( $key ); ?>]" <?php checked( $checked_val ); ?>>
								<span class="slider round"></span>
							</label>
						</div>
					</div>
					<div class="oceanwp-text-block">
						<h3 class="title"><?php echo esc_attr( $title ); ?></h3>
						<?php if ( ! empty( $desc ) ) : ?>
							<div class="oceanwp-widget-description">
								<p class="oceanwp-tp-block-description"><?php echo esc_attr( $desc ); ?></p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php submit_button(); ?>
	</form>
</div>
