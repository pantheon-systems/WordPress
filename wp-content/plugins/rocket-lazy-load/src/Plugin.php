<?php
/**
 * Initialize and load the plugin
 *
 * @package RocketLazyloadPlugin
 */

namespace RocketLazyLoadPlugin;

use League\Container\Container;
use RocketLazyLoadPlugin\EventManagement\EventManager;
use RocketLazyLoadPlugin\Options\Options;

/**
 * Plugin initialize
 *
 * @since 2.0
 * @author Remy Perona
 */
class Plugin
{
    /**
     * Is the plugin loaded
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @var boolean
     */
    private $loaded = false;

    /**
     * Checks if the plugin is loaded
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return boolean
     */
    private function isLoaded()
    {
        return $this->loaded;
    }

    /**
     * Loads the plugin in WordPress
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function load()
    {
        if ($this->isLoaded()) {
            return;
        }

        $container = new Container();

        $container->add('template_path', ROCKET_LL_PATH . 'views/');
        $container->add('plugin_basename', ROCKET_LL_BASENAME);

        $container->add('options', function () {
            return new Options('rocket_lazyload');
        });

        $container->add('event_manager', function () {
            return new EventManager();
        });

        $service_providers = [
            'RocketLazyLoadPlugin\ServiceProvider\OptionServiceProvider',
            'RocketLazyLoadPlugin\ServiceProvider\AdminServiceProvider',
            'RocketLazyLoadPlugin\ServiceProvider\ImagifyNoticeServiceProvider',
            'RocketLazyLoadPlugin\ServiceProvider\LazyloadServiceProvider',
            'RocketLazyLoadPlugin\ServiceProvider\SubscribersServiceProvider',
        ];

        foreach ($service_providers as $service) {
            $container->addServiceProvider($service);
        }

        $subscribers = [
            'RocketLazyLoadPlugin\Subscriber\ThirdParty\AMPSubscriber',
            'RocketLazyLoadPlugin\Subscriber\AdminPageSubscriber',
            'RocketLazyLoadPlugin\Subscriber\ImagifyNoticeSubscriber',
            'RocketLazyLoadPlugin\Subscriber\LazyloadSubscriber',
        ];

        foreach ($subscribers as $subscriber) {
            $container->get('event_manager')->addSubscriber($container->get($subscriber));
        }

        $this->loaded = true;
    }
}
