<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp;

use Google\Site_Kit_Dependencies\GuzzleHttp\Event\HasEmitterTrait;
use Google\Site_Kit_Dependencies\GuzzleHttp\Message\MessageFactory;
use Google\Site_Kit_Dependencies\GuzzleHttp\Message\MessageFactoryInterface;
use Google\Site_Kit_Dependencies\GuzzleHttp\Message\RequestInterface;
use Google\Site_Kit_Dependencies\GuzzleHttp\Message\FutureResponse;
use Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Core;
use Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Future\FutureInterface;
use Google\Site_Kit_Dependencies\GuzzleHttp\Exception\RequestException;
use Google\Site_Kit_Dependencies\React\Promise\FulfilledPromise;
use Google\Site_Kit_Dependencies\React\Promise\RejectedPromise;
/**
 * HTTP client
 */
class Client implements \Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface
{
    use HasEmitterTrait;
    /** @var MessageFactoryInterface Request factory used by the client */
    private $messageFactory;
    /** @var Url Base URL of the client */
    private $baseUrl;
    /** @var array Default request options */
    private $defaults;
    /** @var callable Request state machine */
    private $fsm;
    /**
     * Clients accept an array of constructor parameters.
     *
     * Here's an example of creating a client using an URI template for the
     * client's base_url and an array of default request options to apply
     * to each request:
     *
     *     $client = new Client([
     *         'base_url' => [
     *              'http://www.foo.com/{version}/',
     *              ['version' => '123']
     *          ],
     *         'defaults' => [
     *             'timeout'         => 10,
     *             'allow_redirects' => false,
     *             'proxy'           => '192.168.16.1:10'
     *         ]
     *     ]);
     *
     * @param array $config Client configuration settings
     *     - base_url: Base URL of the client that is merged into relative URLs.
     *       Can be a string or an array that contains a URI template followed
     *       by an associative array of expansion variables to inject into the
     *       URI template.
     *     - handler: callable RingPHP handler used to transfer requests
     *     - message_factory: Factory used to create request and response object
     *     - defaults: Default request options to apply to each request
     *     - emitter: Event emitter used for request events
     *     - fsm: (internal use only) The request finite state machine. A
     *       function that accepts a transaction and optional final state. The
     *       function is responsible for transitioning a request through its
     *       lifecycle events.
     */
    public function __construct(array $config = [])
    {
        $this->configureBaseUrl($config);
        $this->configureDefaults($config);
        if (isset($config['emitter'])) {
            $this->emitter = $config['emitter'];
        }
        $this->messageFactory = isset($config['message_factory']) ? $config['message_factory'] : new \Google\Site_Kit_Dependencies\GuzzleHttp\Message\MessageFactory();
        if (isset($config['fsm'])) {
            $this->fsm = $config['fsm'];
        } else {
            if (isset($config['handler'])) {
                $handler = $config['handler'];
            } elseif (isset($config['adapter'])) {
                $handler = $config['adapter'];
            } else {
                $handler = \Google\Site_Kit_Dependencies\GuzzleHttp\Utils::getDefaultHandler();
            }
            $this->fsm = new \Google\Site_Kit_Dependencies\GuzzleHttp\RequestFsm($handler, $this->messageFactory);
        }
    }
    public function getDefaultOption($keyOrPath = null)
    {
        return $keyOrPath === null ? $this->defaults : \Google\Site_Kit_Dependencies\GuzzleHttp\Utils::getPath($this->defaults, $keyOrPath);
    }
    public function setDefaultOption($keyOrPath, $value)
    {
        \Google\Site_Kit_Dependencies\GuzzleHttp\Utils::setPath($this->defaults, $keyOrPath, $value);
    }
    public function getBaseUrl()
    {
        return (string) $this->baseUrl;
    }
    public function createRequest($method, $url = null, array $options = [])
    {
        $options = $this->mergeDefaults($options);
        // Use a clone of the client's emitter
        $options['config']['emitter'] = clone $this->getEmitter();
        $url = $url || \is_string($url) && \strlen($url) ? $this->buildUrl($url) : (string) $this->baseUrl;
        return $this->messageFactory->createRequest($method, $url, $options);
    }
    public function get($url = null, $options = [])
    {
        return $this->send($this->createRequest('GET', $url, $options));
    }
    public function head($url = null, array $options = [])
    {
        return $this->send($this->createRequest('HEAD', $url, $options));
    }
    public function delete($url = null, array $options = [])
    {
        return $this->send($this->createRequest('DELETE', $url, $options));
    }
    public function put($url = null, array $options = [])
    {
        return $this->send($this->createRequest('PUT', $url, $options));
    }
    public function patch($url = null, array $options = [])
    {
        return $this->send($this->createRequest('PATCH', $url, $options));
    }
    public function post($url = null, array $options = [])
    {
        return $this->send($this->createRequest('POST', $url, $options));
    }
    public function options($url = null, array $options = [])
    {
        return $this->send($this->createRequest('OPTIONS', $url, $options));
    }
    public function send(\Google\Site_Kit_Dependencies\GuzzleHttp\Message\RequestInterface $request)
    {
        $isFuture = $request->getConfig()->get('future');
        $trans = new \Google\Site_Kit_Dependencies\GuzzleHttp\Transaction($this, $request, $isFuture);
        $fn = $this->fsm;
        try {
            $fn($trans);
            if ($isFuture) {
                // Turn the normal response into a future if needed.
                return $trans->response instanceof \Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Future\FutureInterface ? $trans->response : new \Google\Site_Kit_Dependencies\GuzzleHttp\Message\FutureResponse(new \Google\Site_Kit_Dependencies\React\Promise\FulfilledPromise($trans->response));
            }
            // Resolve deep futures if this is not a future
            // transaction. This accounts for things like retries
            // that do not have an immediate side-effect.
            while ($trans->response instanceof \Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Future\FutureInterface) {
                $trans->response = $trans->response->wait();
            }
            return $trans->response;
        } catch (\Exception $e) {
            if ($isFuture) {
                // Wrap the exception in a promise
                return new \Google\Site_Kit_Dependencies\GuzzleHttp\Message\FutureResponse(new \Google\Site_Kit_Dependencies\React\Promise\RejectedPromise($e));
            }
            throw \Google\Site_Kit_Dependencies\GuzzleHttp\Exception\RequestException::wrapException($trans->request, $e);
        } catch (\TypeError $error) {
            $exception = new \Exception($error->getMessage(), $error->getCode(), $error);
            if ($isFuture) {
                // Wrap the exception in a promise
                return new \Google\Site_Kit_Dependencies\GuzzleHttp\Message\FutureResponse(new \Google\Site_Kit_Dependencies\React\Promise\RejectedPromise($exception));
            }
            throw \Google\Site_Kit_Dependencies\GuzzleHttp\Exception\RequestException::wrapException($trans->request, $exception);
        }
    }
    /**
     * Get an array of default options to apply to the client
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $settings = ['allow_redirects' => \true, 'exceptions' => \true, 'decode_content' => \true, 'verify' => \true];
        // Use the standard Linux HTTP_PROXY and HTTPS_PROXY if set.
        // We can only trust the HTTP_PROXY environment variable in a CLI
        // process due to the fact that PHP has no reliable mechanism to
        // get environment variables that start with "HTTP_".
        if (\php_sapi_name() == 'cli' && \getenv('HTTP_PROXY')) {
            $settings['proxy']['http'] = \getenv('HTTP_PROXY');
        }
        if ($proxy = \getenv('HTTPS_PROXY')) {
            $settings['proxy']['https'] = $proxy;
        }
        return $settings;
    }
    /**
     * Expand a URI template and inherit from the base URL if it's relative
     *
     * @param string|array $url URL or an array of the URI template to expand
     *                          followed by a hash of template varnames.
     * @return string
     * @throws \InvalidArgumentException
     */
    private function buildUrl($url)
    {
        // URI template (absolute or relative)
        if (!\is_array($url)) {
            return \strpos($url, '://') ? (string) $url : (string) $this->baseUrl->combine($url);
        }
        if (!isset($url[1])) {
            throw new \InvalidArgumentException('You must provide a hash of ' . 'varname options in the second element of a URL array.');
        }
        // Absolute URL
        if (\strpos($url[0], '://')) {
            return \Google\Site_Kit_Dependencies\GuzzleHttp\Utils::uriTemplate($url[0], $url[1]);
        }
        // Combine the relative URL with the base URL
        return (string) $this->baseUrl->combine(\Google\Site_Kit_Dependencies\GuzzleHttp\Utils::uriTemplate($url[0], $url[1]));
    }
    private function configureBaseUrl(&$config)
    {
        if (!isset($config['base_url'])) {
            $this->baseUrl = new \Google\Site_Kit_Dependencies\GuzzleHttp\Url('', '');
        } elseif (!\is_array($config['base_url'])) {
            $this->baseUrl = \Google\Site_Kit_Dependencies\GuzzleHttp\Url::fromString($config['base_url']);
        } elseif (\count($config['base_url']) < 2) {
            throw new \InvalidArgumentException('You must provide a hash of ' . 'varname options in the second element of a base_url array.');
        } else {
            $this->baseUrl = \Google\Site_Kit_Dependencies\GuzzleHttp\Url::fromString(\Google\Site_Kit_Dependencies\GuzzleHttp\Utils::uriTemplate($config['base_url'][0], $config['base_url'][1]));
            $config['base_url'] = (string) $this->baseUrl;
        }
    }
    private function configureDefaults($config)
    {
        if (!isset($config['defaults'])) {
            $this->defaults = $this->getDefaultOptions();
        } else {
            $this->defaults = \array_replace($this->getDefaultOptions(), $config['defaults']);
        }
        // Add the default user-agent header
        if (!isset($this->defaults['headers'])) {
            $this->defaults['headers'] = ['User-Agent' => \Google\Site_Kit_Dependencies\GuzzleHttp\Utils::getDefaultUserAgent()];
        } elseif (!\Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Core::hasHeader($this->defaults, 'User-Agent')) {
            // Add the User-Agent header if one was not already set
            $this->defaults['headers']['User-Agent'] = \Google\Site_Kit_Dependencies\GuzzleHttp\Utils::getDefaultUserAgent();
        }
    }
    /**
     * Merges default options into the array passed by reference.
     *
     * @param array $options Options to modify by reference
     *
     * @return array
     */
    private function mergeDefaults($options)
    {
        $defaults = $this->defaults;
        // Case-insensitively merge in default headers if both defaults and
        // options have headers specified.
        if (!empty($defaults['headers']) && !empty($options['headers'])) {
            // Create a set of lowercased keys that are present.
            $lkeys = [];
            foreach (\array_keys($options['headers']) as $k) {
                $lkeys[\strtolower($k)] = \true;
            }
            // Merge in lowercase default keys when not present in above set.
            foreach ($defaults['headers'] as $key => $value) {
                if (!isset($lkeys[\strtolower($key)])) {
                    $options['headers'][$key] = $value;
                }
            }
            // No longer need to merge in headers.
            unset($defaults['headers']);
        }
        $result = \array_replace_recursive($defaults, $options);
        foreach ($options as $k => $v) {
            if ($v === null) {
                unset($result[$k]);
            }
        }
        return $result;
    }
    /**
     * @deprecated Use {@see GuzzleHttp\Pool} instead.
     * @see GuzzleHttp\Pool
     */
    public function sendAll($requests, array $options = [])
    {
        \Google\Site_Kit_Dependencies\GuzzleHttp\Pool::send($this, $requests, $options);
    }
    /**
     * @deprecated Use GuzzleHttp\Utils::getDefaultHandler
     */
    public static function getDefaultHandler()
    {
        return \Google\Site_Kit_Dependencies\GuzzleHttp\Utils::getDefaultHandler();
    }
    /**
     * @deprecated Use GuzzleHttp\Utils::getDefaultUserAgent
     */
    public static function getDefaultUserAgent()
    {
        return \Google\Site_Kit_Dependencies\GuzzleHttp\Utils::getDefaultUserAgent();
    }
}
