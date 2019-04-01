<?php
/**
 * Admin Page view
 *
 * @package RocketLazyloadPlugin
 */

defined('ABSPATH') || die('Cheatin\' uh?');

global $wp_version;

$options = [
    'images'  => [
        'label' => __('Images', 'rocket-lazy-load'),
    ],
    'iframes' => [
        'label' => __('Iframes &amp; Videos', 'rocket-lazy-load'),
    ],
    'youtube' => [
        'label' => __('Replace Youtube videos by thumbnail', 'rocket-lazy-load'),
    ],
];

?>
<div class="wrap rocket-lazyload-settings">

    <?php $heading_tag = version_compare($wp_version, '4.3') >= 0 ? 'h1' : 'h2'; ?>
    <<?php echo $heading_tag; ?> class="screen-reader-text"><?php echo esc_html(get_admin_page_title()); ?></<?php echo $heading_tag; ?>>
    <div class="rocket-lazyload-header">
        <div>
            <p class="rocket-lazyload-title"><img src="<?php echo esc_url(ROCKET_LL_ASSETS_URL . 'img/logo.png'); ?>" srcset="<?php echo esc_url(ROCKET_LL_ASSETS_URL . 'img/logo@2x.png'); ?> 2x" alt="<?php echo esc_attr(get_admin_page_title()); ?>" width="216" height="59"></p>
            <p class="rocket-lazyload-subtitle"><?php esc_html_e('Settings', 'rocket-lazy-load'); ?></p>
        </div>
        <?php $rocket_lazyload_rate_url = 'https://wordpress.org/support/plugin/rocket-lazy-load/reviews/?rate=5#postform'; ?>
        <p class="rocket-lazyload-rate-us">
            <?php
            // Translators: %1$s is a <strong> tag, %2$s is </strong><br>, %3$s is the complete link tag to Rocket Lazy Load review form, %4$s is the closing </a> tag.
            printf(__('%1$sDo you like this plugin?%2$s Please take a few seconds to %3$srate it on WordPress.org%4$s!', 'rocket-lazy-load'), '<strong>', '</strong><br>', '<a href="' . $rocket_lazyload_rate_url . '">', '</a>');
            ?>
            <br>
            <a class="stars" href="<?php echo $rocket_lazyload_rate_url; ?>"><?php echo str_repeat('<span class="dashicons dashicons-star-filled"></span>', 5); ?></a>
        </p>
    </div>
    <div class="rocket-lazyload-body">
        <form action="options.php" class="rocket-lazyload-form" method="post">
            <fieldset>
                <legend class="screen-reader-text"><?php esc_html_e('Lazyload', 'rocket-lazy-load'); ?></legend>
                <p><?php esc_html_e('LazyLoad displays images, iframes and videos on a page only when they are visible to the user.', 'rocket-lazy-load'); ?></p>
                <p><?php esc_html_e('This mechanism reduces the number of HTTP requests and improves the loading time.', 'rocket-lazy-load'); ?></p>
                <ul class="rocket-lazyload-options">
                    <?php foreach ($options as $slug => $infos) : ?>
                    <li class="rocket-lazyload-option">
                        <input type="checkbox" value="1" id="lazyload-<?php echo esc_attr($slug); ?>" name="rocket_lazyload_options[<?php echo esc_attr($slug); ?>]" <?php checked($this->option_array->get($slug, 0), 1); ?> aria-labelledby="describe-lazyload-<?php echo esc_attr($slug); ?>">
                        <label for="lazyload-<?php echo esc_attr($slug); ?>">
                            <span id="describe-lazyload-<?php echo esc_attr($slug); ?>" class="rocket-lazyload-label-description"><?php echo esc_html($infos['label']); ?></span>
                        </label>
                    </li>

                    <?php endforeach; ?>

                </ul>
            </fieldset>
        <?php settings_fields('rocket_lazyload'); ?>

        <?php if (! is_plugin_active('wp-rocket/wp-rocket.php')) { ?>
        <div class="rocket-lazyload-upgrade">

            <div class="rocket-lazyload-upgrade-cta">
                <p class="rocket-lazyload-subtitle"><?php esc_html_e('We recommend for you', 'rocket-lazy-load'); ?></p>
                <p class="rocket-lazyload-bigtext">
                    <?php esc_html_e('Go Premium with', 'rocket-lazy-load'); ?>
                    <img class="rocket-lazyload-rocket-logo" src="<?php echo esc_url(ROCKET_LL_ASSETS_URL . 'img/wprocket.png'); ?>" srcset="<?php echo esc_url(ROCKET_LL_ASSETS_URL . 'img/wprocket@2x.png'); ?>" width="232" height="63" alt="WP Rocket">
                </p>

                <div class="rocket-lazyload-cta-block">
                    <a class="button button-primary" href="https://wp-rocket.me/?utm_source=wp_plugin&utm_medium=rocket_lazyload"><?php _e('Get WP&nbsp;Rocket Now!', 'rocket-lazy-load'); ?></a>
                </div>
            </div><!-- .rocket-lazyload-upgrade-cta -->

            <div class="rocket-lazyload-upgrade-arguments">
                <ul>
                    <li class="rll-upgrade-item">
                    <?php
                    // Translators: %1$s = strong opening tag, %2$s = strong closing tag.
                    printf(__('%1$sMultiple new features%2$s to further improve your load time', 'rocket-lazy-load'), '<strong>', '</strong>')
                    ?>
                    </li>
                    <li class="rll-upgrade-item">
                    <?php
                    // Translators: %1$s = strong opening tag, %2$s = strong closing tag.
                    printf(__('All you need to %1$simprove your Google PageSpeed%2$s score', 'rocket-lazy-load'), '<strong>', '</strong>')
                    ?>
                    </li>
                    <li class="rll-upgrade-item">
                    <?php
                    // Translators: %1$s = strong opening tag, %2$s = strong closing tag.
                    printf(__('%1$sBoost your SEO%2$s by preloading your cache page for Google’s bots', 'rocket-lazy-load'), '<strong>', '</strong>')
                    ?>
                    </li>
                    <li class="rll-upgrade-item">
                    <?php
                    // Translators: %1$s = strong opening tag, %2$s = strong closing tag.
                    printf(__('Watch your conversion rise with the %1$s100%% WooCommerce compatibility%2$s', 'rocket-lazy-load'), '<strong>', '</strong>')
                    ?>
                    </li>
                    <li class="rll-upgrade-item">
                    <?php
                    // Translators: %1$s = strong opening tag, %2$s = strong closing tag.
                    printf(__('Minimal configuration, %1$sImmediate results%2$s', 'rocket-lazy-load'), '<strong>', '</strong>')
                    ?>
                    </li>
                    <li class="rll-upgrade-item">
                    <?php
                    // Translators: %1$s = strong opening tag, %2$s = strong closing tag.
                    printf(__('Set up takes %1$s5 minutes flat%2$s', 'rocket-lazy-load'), '<strong>', '</strong>')
                    ?>
                    </li>
                    <li class="rll-upgrade-item">
                    <?php
                    // Translators: %1$s = strong opening tag, %2$s = strong closing tag.
                    printf(__('%1$s24/7 support%2$s', 'rocket-lazy-load'), '<strong>', '</strong>')
                    ?>
                    </li>
                </ul>
            </div><!-- .rocket-lazyload-upgrade-arguments -->

        </div><!-- .rocket-lazyload-upgrade -->
        <?php } ?>

        <p class="submit">
            <button type="submit" class="button button-primary">
                <span class="text"><?php esc_html_e('Save changes', 'rocket-lazy-load'); ?></span>
                <span class="icon">✓</span>
            </button>
        </p>
        </form>
    </div>
</div>
