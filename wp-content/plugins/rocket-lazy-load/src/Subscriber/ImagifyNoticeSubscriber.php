<?php
/**
 * Imagify Notice subscriber
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\Subscriber;

defined('ABSPATH') || die('Cheatin\' uh?');

use RocketLazyLoadPlugin\EventManagement\SubscriberInterface;
use RocketLazyLoadPlugin\Admin\ImagifyNotice;

/**
 * Imagify Notice Subscriber
 *
 * @since 2.0
 * @author Remy Perona
 */
class ImagifyNoticeSubscriber implements SubscriberInterface
{
    /**
     * ImagifyNotice instance
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @var ImagifyNotice
     */
    private $imagify_notice;

    /**
     * Constructor
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @param ImagifyNotice $imagify_notice ImagifyNotice instance.
     */
    public function __construct(ImagifyNotice $imagify_notice)
    {
        $this->imagify_notice = $imagify_notice;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [
            'admin_notices'                              => 'imagifyNotice',
            'admin_footer-settings_page_rocket_lazyload' => 'dismissNoticeJS',
            'wp_ajax_rocket_lazyload_ignore'             => 'dismissBoxes',
            'admin_post_rocket_lazyload_ignore'          => 'dismissBoxes',
        ];
    }

    /**
     * Displays the Imagify notice
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function imagifyNotice()
    {
        $current_screen = get_current_screen();

        if ('admin_notices' === current_filter() && ( isset($current_screen) && 'settings_page_rocket_lazyload' !== $current_screen->base )) {
            return;
        }
    
        $boxes = get_user_meta(get_current_user_id(), 'rocket_lazyload_boxes', true);
    
        if (defined('IMAGIFY_VERSION') || in_array('rocket_lazyload_imagify_notice', (array) $boxes, true) || 1 === get_option('rocket_lazyload_dismiss_imagify_notice') || ! current_user_can('manage_options')) {
            return;
        }

        $this->imagify_notice->displayNotice();
    }

    /**
     * Inserts the javascript to dismiss the notice
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function dismissNoticeJS()
    {
        echo "<script>
        jQuery( document ).ready( function( $ ){
            $( '.rktll-cross' ).on( 'click', function( e ) {
                e.preventDefault();
                var url = $( this ).attr( 'href' ).replace( 'admin-post', 'admin-ajax' );
                $.get( url ).done( $( this ).parent().hide( 'slow' ) );
            });
        } );
        </script>";
    }

    /**
     * Saves the dismiss for the user
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function dismissBoxes()
    {
        if (! isset($_GET['box'], $_GET['action'], $_GET['_wpnonce'])) {
            return;
        }

        if (! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'rocket_lazyload_ignore_rocket_lazyload_imagify_notice')) {
            if (defined('DOING_AJAX')) {
                wp_send_json(['error' => 1]);
            } else {
                wp_nonce_ays('');
            }
        }

        $box = sanitize_key(wp_unslash($_GET['box']));

        if ('rocket_lazyload_imagify_notice' === $box) {
            update_option('rocket_lazyload_dismiss_imagify_notice', 0);
        }

        $actual = (array) get_user_meta(get_current_user_id(), 'rocket_lazyload_boxes', true);
        $actual = array_merge($actual, [ $box ]);
        $actual = array_filter($actual);
        $actual = array_unique($actual);

        update_user_meta(get_current_user_id(), 'rocket_lazyload_boxes', $actual);
        delete_transient($box);

        if (empty($GLOBALS['pagenow']) || 'admin-post.php' !== $GLOBALS['pagenow']) {
            return;
        }

        if (defined('DOING_AJAX')) {
            wp_send_json(['error' => 0]);
        } else {
            wp_safe_redirect(esc_url_raw(wp_get_referer()));
            die();
        }
    }
}
