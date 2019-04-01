<?php
/**
 * Service Provider for the admin page classes
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\ServiceProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Adds the admin page to the container
 *
 * @since 2.0
 * @author Remy Perona
 */
class AdminServiceProvider extends AbstractServiceProvider
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
        'RocketLazyLoadPlugin\Admin\AdminPage',
    ];

    /**
     * Registers the admin page in the container
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->add('RocketLazyLoadPlugin\Admin\AdminPage')
            ->withArgument($this->getContainer()->get('options'))
            ->withArgument($this->getContainer()->get('RocketLazyLoadPlugin\Options\OptionArray'))
            ->withArgument($this->getContainer()->get('template_path'));
    }
}
