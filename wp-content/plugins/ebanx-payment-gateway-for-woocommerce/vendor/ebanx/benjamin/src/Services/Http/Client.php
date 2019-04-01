<?php
namespace Ebanx\Benjamin\Services\Http;

use GuzzleHttp;

class Client
{
    const SANDBOX_URL = 'https://sandbox.ebanxpay.com/';
    const LIVE_URL = 'https://api.ebanxpay.com/';

    const MODE_SANDBOX = 0;
    const MODE_LIVE = 1;

    const SUCCESS = 'SUCCESS';
    const ERROR = 'ERROR';

    /**
     * @var GuzzleHttp\Client
     */
    protected $engine = null;

    /**
     * @var integer
     */
    private $mode = self::MODE_SANDBOX;

    public function __construct()
    {
        $this->engine = new Engine();
    }

    protected function html($url)
    {
        return $this->engine->get($url)->getContents();
    }

    /**
     * @param  object|array $data Any data you want to send
     * @param  string       $endpoint The API endpoint you want to call
     * @return array
     */
    protected function post($data, $endpoint)
    {
        return $this->engine->post(
            $this->getUrl() . $endpoint,
            $data
        )->json();
    }

    /**
     * @param  object|array $data Any data you want to send
     * @param  string       $endpoint The API endpoint you want to call
     * @return array
     */
    protected function query($data, $endpoint)
    {
        return $this->engine->get(
            $this->getUrl() . $endpoint,
            $data
        )->json();
    }

    /**
     * @param  object|array $data Payment data payload
     * @return array
     */
    public function payment($data)
    {
        return $this->post($data, 'ws/direct');
    }

    /**
     * @param  object|array $data Payment data payload
     * @return array
     */
    public function request($data)
    {
        return $this->post($data, 'ws/request');
    }

    /**
     * @param  object|array $data Payment data payload
     * @return array
     */
    public function refund($data)
    {
        return $this->query($data, 'ws/refund');
    }

    /**
     * @param  object|array $data Payment data payload
     * @return array
     */
    public function cancel($data)
    {
        return $this->query($data, 'ws/cancel');
    }

    /**
     * @param  object|array $data Payment data payload
     * @return array
     */
    public function capture($data)
    {
        return $this->query($data, 'ws/capture');
    }

    /**
     * @param  object|array $data Exchange data payload
     * @return array
     */
    public function exchange($data)
    {
        return $this->query($data, 'ws/exchange');
    }

    public function paymentInfo($data)
    {
        return $this->query($data, 'ws/query');
    }

    public function fetchContent($url)
    {
        return $this->html($url);
    }

    /**
     * @param object|array $data
     *
     * @return array
     */
    public function validatePrivateKey($data)
    {
        return $this->query($data, 'ws/merchantIntegrationProperties/get');
    }

    /**
     * @param object|array $data
     *
     * @return array
     */
    public function validatePublicKey($data)
    {
        return $this->query($data, 'ws/merchantIntegrationProperties/isValidPublicIntegrationKey');
    }

    /**
     * Current endpoint url
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->mode === self::MODE_LIVE) {
            return self::LIVE_URL;
        }

        return self::SANDBOX_URL;
    }

    /**
     * Sets the client to sandbox mode
     *
     * @return Client
     */
    public function inSandboxMode()
    {
        $this->mode = self::MODE_SANDBOX;
        return $this;
    }

    /**
     * Sets the client to live mode
     *
     * @return Client
     */
    public function inLiveMode()
    {
        $this->mode = self::MODE_LIVE;
        return $this;
    }

    /**
     * @param  bool $toSandbox Switch to sandbox(true) or live(false) modes
     *
     * @return Client
     */
    public function switchMode($toSandbox)
    {
        if ($toSandbox) {
            return $this->inSandboxMode();
        }

        return $this->inLiveMode();
    }

    /**
     * @return integer
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return boolean
     */
    public function isSandbox()
    {
        return $this->mode === self::MODE_SANDBOX;
    }

    public function addUserAgentInfo($userData)
    {
        $this->engine->addUserAgentInfo($userData);
    }

    public function getUserAgentInfo()
    {
        return $this->engine->getUserAgentInfo();
    }
}
