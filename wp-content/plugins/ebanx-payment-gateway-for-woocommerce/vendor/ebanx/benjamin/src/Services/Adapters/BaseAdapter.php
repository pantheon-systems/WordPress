<?php
namespace Ebanx\Benjamin\Services\Adapters;

use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\Country;

abstract class BaseAdapter
{
    /**
     * @var Config
     */
    protected $config;

    protected function getIntegrationKey()
    {
        return $this->config->isSandbox
            ? $this->config->sandboxIntegrationKey
            : $this->config->integrationKey;
    }

    protected function getNotificationUrl()
    {
        return isset($this->config->notificationUrl)
            ? $this->config->notificationUrl
            : '';
    }

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return object
     */
    abstract public function transform();
}
