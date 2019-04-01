<?php
/**
 * Service Provider for the lazyload library
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\ServiceProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Adds the lazyload library to the container
 *
 * @since 2.0
 * @author Remy Perona
 */
class LazyloadServiceProvider extends AbstractServiceProvider
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
        'RocketLazyload\Assets',
        'RocketLazyload\Image',
        'RocketLazyload\Iframe',
    ];

    /**
     * Registers the lazyload library in the container
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->add('RocketLazyload\Assets');
        $this->getContainer()->add('RocketLazyload\Image');
        $this->getContainer()->add('RocketLazyload\Iframe');
    }
}
