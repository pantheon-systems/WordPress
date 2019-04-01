<?php
/**
 * Service Provider for the plugin subscribers
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\ServiceProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Adds the subscribers to the container
 *
 * @since 2.0
 * @author Remy Perona
 */
class SubscribersServiceProvider extends AbstractServiceProvider
{
    /**
     * Data provided by the service provider
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @var array
     */
    protected $provides = [
        'RocketLazyLoadPlugin\Subscriber\ThirdParty\AMPSubscriber',
        'RocketLazyLoadPlugin\Subscriber\AdminPageSubscriber',
        'RocketLazyLoadPlugin\Subscriber\ImagifyNoticeSubscriber',
        'RocketLazyLoadPlugin\Subscriber\LazyloadSubscriber',
    ];

    /**
     * Registers the subscribers in the container
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->add('RocketLazyLoadPlugin\Subscriber\ThirdParty\AMPSubscriber');

        $this->getContainer()->add('RocketLazyLoadPlugin\Subscriber\AdminPageSubscriber')
            ->withArgument($this->getContainer()->get('RocketLazyLoadPlugin\Admin\AdminPage'))
            ->withArgument($this->getContainer()->get('plugin_basename'));

        $this->getContainer()->add('RocketLazyLoadPlugin\Subscriber\ImagifyNoticeSubscriber')
            ->withArgument($this->getContainer()->get('RocketLazyLoadPlugin\Admin\ImagifyNotice'));

        $this->getContainer()->add('RocketLazyLoadPlugin\Subscriber\LazyloadSubscriber')
            ->withArgument($this->getContainer()->get('RocketLazyLoadPlugin\Options\OptionArray'))
            ->withArgument($this->getContainer()->get('RocketLazyload\Assets'))
            ->withArgument($this->getContainer()->get('RocketLazyload\Image'))
            ->withArgument($this->getContainer()->get('RocketLazyload\Iframe'));
    }
}
