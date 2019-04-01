<?php
/**
 * Service Provider for the imagify notice class
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\ServiceProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Adds the Imagify notice to the container
 *
 * @since 2.0
 * @author Remy Perona
 */
class ImagifyNoticeServiceProvider extends AbstractServiceProvider
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
        'RocketLazyLoadPlugin\Admin\ImagifyNotice',
    ];

    /**
     * Registers the Imagify notice in the container
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->add('RocketLazyLoadPlugin\Admin\ImagifyNotice')
            ->withArgument($this->getContainer()->get('template_path'));
    }
}
