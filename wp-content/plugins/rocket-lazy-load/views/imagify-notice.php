<?php
/**
 * Imagify notice view
 *
 * @package RocketLazyloadPlugin
 */

defined('ABSPATH') || die('Cheatin\' uh?');

$action_url = wp_nonce_url(
    add_query_arg(
        [
            'action' => 'install-plugin',
            'plugin' => 'imagify',
        ],
        admin_url('update.php')
    ),
    'install-plugin_imagify'
); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound.

$dismiss_url = wp_nonce_url(
    admin_url('admin-post.php?action=rocket_lazyload_ignore&box=rocket_lazyload_imagify_notice'),
    'rocket_lazyload_ignore_rocket_lazyload_imagify_notice'
); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound.

?>
<div id="plugin-filter" class="updated plugin-card plugin-card-imagify rktll-imagify-notice">
<a href="<?php echo esc_url($dismiss_url); ?>" class="rktll-cross"><span class="dashicons dashicons-no"></span></a>

<p class="rktll-imagify-logo">
    <img src="<?php echo esc_url(ROCKET_LL_ASSETS_URL . 'img/logo-imagify.png'); ?>" srcset="<?php echo esc_attr(ROCKET_LL_ASSETS_URL . 'img/logo-imagify.svg 2x'); ?>" alt="Imagify" width="150" height="18">
</p>
<p class="rktll-imagify-msg">
    <?php esc_html_e('Speed up your website and boost your SEO by reducing image file sizes without losing quality with Imagify.', 'rocket-lazy-load'); ?>
</p>
<p class="rktll-imagify-cta">
    <a data-slug="imagify" href="<?php echo esc_url($action_url); ?>" class="button button-primary install-now"><?php esc_html_e('Install Imagify for Free', 'rocket-lazy-load'); ?></a>
</p>
</div>
