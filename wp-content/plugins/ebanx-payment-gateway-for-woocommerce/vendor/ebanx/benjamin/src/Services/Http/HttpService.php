<?php
namespace Ebanx\Benjamin\Services\Http;

use Ebanx\Benjamin\Models\Configs\Config;

abstract class HttpService
{
    protected $config;
    protected $client;

    public function __construct(Config $config, Client $client = null)
    {
        $this->config = $config;
        $this->client = $this->client ?: $client;

        if (!$this->client) {
            $this->client = new Client();
            $this->switchMode(null);
        }
    }

    /**
     * @param  bool|null $toSandbox Switch to default(null) sandbox(true) or live(false) modes
     * @return void
     */
    protected function switchMode($toSandbox)
    {
        if ($toSandbox === null) {
            $toSandbox = $this->config->isSandbox;
        }

        $this->client->switchMode($toSandbox);
    }
}
