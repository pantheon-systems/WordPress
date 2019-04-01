<?php
/**
 * Admin Page subscriber
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\Subscriber;

defined('ABSPATH') || die('Cheatin\' uh?');

use RocketLazyLoadPlugin\EventManagement\SubscriberInterface;
use RocketLazyLoadPlugin\Admin\AdminPage;

/**
 * Admin Page Subscriber
 *
 * @since 2.0
 * @author Remy Perona
 */
class AdminPageSubscriber implements SubscriberInterface
{
    /**
     * AdminPage instance
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @var AdminPage
     */
    private $page;

    /**
     * Plugin basename
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @var string
     */
    private $plugin_basename;

    /**
     * Constructor
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @param AdminPage $page AdminPage instance.
     * @param string    $plugin_basename Plugin basename.
     */
    public function __construct(AdminPage $page, $plugin_basename)
    {
        $this->page            = $page;
        $this->plugin_basename = $plugin_basename;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [
            'admin_init'                                      => 'configure',
            'admin_menu'                                      => 'addAdminPage',
            'plugin_action_links_' . $this->plugin_basename   => 'addPluginPageLink',
            'admin_enqueue_scripts'                           => 'enqueueAdminStyle',
        ];
    }

    /**
     * Registers the plugin settings in WordPress
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function configure()
    {
        $this->page->configure();
    }

    /**
     * Adds the admin page to the settings menu
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function addAdminPage()
    {
        add_options_page(
            $this->page->getPageTitle(),
            $this->page->getMenuTitle(),
            $this->page->getCapability(),
            $this->page->getSlug(),
            [ $this->page, 'renderPage' ]
        );
    }

    /**
     * Adds a link to the plugin settings on the plugins page
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @param array $actions Actions for the plugin.
     * @return array
     */
    public function addPluginPageLink($actions)
    {
        array_unshift(
            $actions,
            sprintf(
                '<a href="%s">%s</a>',
                admin_url('options-general.php?page=' . $this->page->getSlug()),
                __('Settings', 'rocket-lazy-load')
            )
        );

        return $actions;
    }

    /**
     * Enqueue the css for the option page
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @param string $hook_suffix Current page hook.
     */
    public function enqueueAdminStyle($hook_suffix)
    {
        if ('settings_page_rocket_lazyload' !== $hook_suffix) {
            return;
        }
    
        wp_enqueue_style('rocket-lazyload', ROCKET_LL_ASSETS_URL . 'css/admin.css', null, ROCKET_LL_VERSION);
    }
}
