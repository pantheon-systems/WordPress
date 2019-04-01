<?php
/**
 * Service Provider for the plugin options
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\ServiceProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Adds the option array to the container
 *
 * @since 2.0
 * @author Remy Perona
 */
class OptionServiceProvider extends AbstractServiceProvider
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
        'RocketLazyLoadPlugin\Options\OptionArray',
    ];

    /**
     * Registers the option array in the container
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->add('RocketLazyLoadPlugin\Options\OptionArray')
            ->withArgument($this->getContainer()->get('options')->get('_options'));
    }
}
