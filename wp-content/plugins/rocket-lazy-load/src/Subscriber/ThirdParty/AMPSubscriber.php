<?php
/**
 * AMP plugin compatibility subscrber
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\Subscriber\ThirdParty;

use RocketLazyLoadPlugin\EventManagement\EventManager;
use RocketLazyLoadPlugin\EventManagement\EventManagerAwareSubscriberInterface;

defined('ABSPATH') || die('Cheatin\' uh?');

/**
 * Manages compatibility with the AMP plugin
 *
 * @since 2.0
 * @author Remy Perona
 */
class AMPSubscriber implements EventManagerAwareSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [
            'wp' => 'disableIfAMP',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setEventManager(EventManager $event_manager)
    {
        $this->event_manager = $event_manager;
    }

    /**
     * Disable if on AMP page
     *
     * @since 2.0.2
     * @author Remy Perona
     *
     * @return void
     */
    public function disableIfAMP()
    {
        if ($this->isAmpEndpoint()) {
            $this->event_manager->addCallback('do_rocket_lazyload', '__return_false');
            $this->event_manager->addCallback('do_rocket_lazyload_iframes', '__return_false');
        }
    }

    /**
     * Checks if current page uses AMP
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return boolean
     */
    private function isAmpEndpoint()
    {
        if (function_exists('is_amp_endpoint') && \is_amp_endpoint()) {
            return true;
        }

        return false;
    }
}
