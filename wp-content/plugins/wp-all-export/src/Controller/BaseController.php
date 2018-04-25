<?php

namespace Wpae\Controller;


use Wpae\Di\WpaeDi;
use Wpae\Security\AccessControl;

abstract class BaseController
{
    /** @var WpaeDi */
    private $container;

    public function __construct(WpaeDi $container)
    {
        $this->container = $container;
    }

    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * //TODO: Add in container
     * @return AccessControl
     */
    public function getAccessControl()
    {
        return new AccessControl();
    }
}