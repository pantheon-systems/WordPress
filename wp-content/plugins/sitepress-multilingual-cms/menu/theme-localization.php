<?php
global $sitepress;

if( ( !isset( $sitepress_settings['existing_content_language_verified'] ) ||
      ! $sitepress_settings['existing_content_language_verified']) ||
      2 > count($sitepress->get_active_languages() ) ) {
    return;
}

?>

<div class="wrap">
    <h2><?php _e('Theme and plugins localization', 'sitepress') ?></h2>

<?php

	/** @deprecated use wpml_custom_localization_type instead */
	do_action('icl_custom_localization_type');

	do_action('wpml_custom_localization_type');
?>

    <?php do_action('icl_menu_footer'); ?>
</div>
