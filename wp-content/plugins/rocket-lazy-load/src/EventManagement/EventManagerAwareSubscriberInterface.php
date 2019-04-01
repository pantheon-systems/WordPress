<?php
/**
 * Interface for subscribers who need access to the event manager object
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\EventManagement;

interface EventManagerAwareSubscriberInterface extends SubscriberInterface
{
    /**
     * Set the WordPress event manager for the subscriber.
     *
     * @since 3.1
     * @author Remy Perona
     *
     * @param EventManager $event_manager EventManager instance.
     */
    public function setEventManager(EventManager $event_manager);
}
