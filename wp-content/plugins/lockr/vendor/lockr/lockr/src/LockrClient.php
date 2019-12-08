<?php
namespace Lockr;

use GuzzleHttp;
use GuzzleHttp\Psr7;

use Lockr\Exception\LockrApiException;

class LockrClient
{
    const VERSION = '1.0.4';

    /** @var GuzzleHttp\ClientInterface $httpClient */
    private $httpClient;

    /** @var LockrStatsInterface $stats */
    private $stats;

    /** @var bool $hasCert */
    private $hasCert = false;

    /**
     * @param GuzzleHttp\ClientInterface $http_client
     */
    public function __construct(
        GuzzleHttp\ClientInterface $http_client,
        LockrStatsInterface $stats = null
    ) {
        $this->httpClient = $http_client;
        $this->stats = $stats ?: new BlackholeStats();
        $this->hasCert = (bool) $http_client->getConfig('cert');
    }

    /**
     * Whether the request will have a client cert attached.
     *
     * @return bool
     */
    public function hasCert()
    {
        return $this->hasCert;
    }

    /**
     * Gets the underlying HTTP client.
     *
     * @return GuzzleHttp\ClientInterface
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Returns the stats handler.
     *
     * @return LockrStatsInterface
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * @param SettingsInterface $settings
     */
    public static function createFromSettings(SettingsInterface $settings)
    {
        $ua = 'php/' . phpversion() . ' LockrClient/' . self::VERSION;
        $base_options = [
            'base_uri' => "https://{$settings->getHostname()}",
            'connect_timeout' => 2.0,
            'expect' => false,
            'headers' => [
                'accept' => ['application/json'],
                'user-agent' => [$ua],
            ],
            'http_errors' => false,
            'read_timeout' => 3.0,
            'timeout' => 5.0,
        ];
        $options = array_replace($base_options, $settings->getOptions());
        $client = new GuzzleHttp\Client($options);
        return new static($client);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function query(array $data)
    {
        $resp = $this->httpClient->request('POST', '/graphql', [
            'json' => $data,
        ]);
        $resp_data = json_decode((string) $resp->getBody(), true);
        if (!empty($resp_data['errors'])) {
            throw new LockrApiException($resp_data['errors']);
        }
        return $resp_data['data'];
    }
}

// ex: ts=4 sts=4 sw=4 et:
