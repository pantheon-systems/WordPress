<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api;

use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Serializer\QuerySerializer;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Serializer\Ec2ParamBuilder;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Parser\QueryParser;
/**
 * Represents a web service API model.
 */
class Service extends \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\AbstractModel
{
    /** @var callable */
    private $apiProvider;
    /** @var string */
    private $serviceName;
    /** @var string */
    private $apiVersion;
    /** @var Operation[] */
    private $operations = [];
    /** @var array */
    private $paginators = null;
    /** @var array */
    private $waiters = null;
    /**
     * @param array    $definition
     * @param callable $provider
     *
     * @internal param array $definition Service description
     */
    public function __construct(array $definition, callable $provider)
    {
        static $defaults = ['operations' => [], 'shapes' => [], 'metadata' => []], $defaultMeta = ['apiVersion' => null, 'serviceFullName' => null, 'serviceId' => null, 'endpointPrefix' => null, 'signingName' => null, 'signatureVersion' => null, 'protocol' => null, 'uid' => null];
        $definition += $defaults;
        $definition['metadata'] += $defaultMeta;
        $this->definition = $definition;
        $this->apiProvider = $provider;
        parent::__construct($definition, new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\ShapeMap($definition['shapes']));
        if (isset($definition['metadata']['serviceIdentifier'])) {
            $this->serviceName = $this->getServiceName();
        } else {
            $this->serviceName = $this->getEndpointPrefix();
        }
        $this->apiVersion = $this->getApiVersion();
    }
    /**
     * Creates a request serializer for the provided API object.
     *
     * @param Service $api      API that contains a protocol.
     * @param string  $endpoint Endpoint to send requests to.
     *
     * @return callable
     * @throws \UnexpectedValueException
     */
    public static function createSerializer(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Service $api, $endpoint)
    {
        static $mapping = ['json' => 'DeliciousBrains\\WP_Offload_Media\\Aws3\\Aws\\Api\\Serializer\\JsonRpcSerializer', 'query' => 'DeliciousBrains\\WP_Offload_Media\\Aws3\\Aws\\Api\\Serializer\\QuerySerializer', 'rest-json' => 'DeliciousBrains\\WP_Offload_Media\\Aws3\\Aws\\Api\\Serializer\\RestJsonSerializer', 'rest-xml' => 'DeliciousBrains\\WP_Offload_Media\\Aws3\\Aws\\Api\\Serializer\\RestXmlSerializer'];
        $proto = $api->getProtocol();
        if (isset($mapping[$proto])) {
            return new $mapping[$proto]($api, $endpoint);
        }
        if ($proto == 'ec2') {
            return new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Serializer\QuerySerializer($api, $endpoint, new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Serializer\Ec2ParamBuilder());
        }
        throw new \UnexpectedValueException('Unknown protocol: ' . $api->getProtocol());
    }
    /**
     * Creates an error parser for the given protocol.
     *
     * @param string $protocol Protocol to parse (e.g., query, json, etc.)
     *
     * @return callable
     * @throws \UnexpectedValueException
     */
    public static function createErrorParser($protocol)
    {
        static $mapping = ['json' => 'DeliciousBrains\\WP_Offload_Media\\Aws3\\Aws\\Api\\ErrorParser\\JsonRpcErrorParser', 'query' => 'DeliciousBrains\\WP_Offload_Media\\Aws3\\Aws\\Api\\ErrorParser\\XmlErrorParser', 'rest-json' => 'DeliciousBrains\\WP_Offload_Media\\Aws3\\Aws\\Api\\ErrorParser\\RestJsonErrorParser', 'rest-xml' => 'DeliciousBrains\\WP_Offload_Media\\Aws3\\Aws\\Api\\ErrorParser\\XmlErrorParser', 'ec2' => 'DeliciousBrains\\WP_Offload_Media\\Aws3\\Aws\\Api\\ErrorParser\\XmlErrorParser'];
        if (isset($mapping[$protocol])) {
            return new $mapping[$protocol]();
        }
        throw new \UnexpectedValueException("Unknown protocol: {$protocol}");
    }
    /**
     * Applies the listeners needed to parse client models.
     *
     * @param Service $api API to create a parser for
     * @return callable
     * @throws \UnexpectedValueException
     */
    public static function createParser(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Service $api)
    {
        static $mapping = ['json' => 'DeliciousBrains\\WP_Offload_Media\\Aws3\\Aws\\Api\\Parser\\JsonRpcParser', 'query' => 'DeliciousBrains\\WP_Offload_Media\\Aws3\\Aws\\Api\\Parser\\QueryParser', 'rest-json' => 'DeliciousBrains\\WP_Offload_Media\\Aws3\\Aws\\Api\\Parser\\RestJsonParser', 'rest-xml' => 'DeliciousBrains\\WP_Offload_Media\\Aws3\\Aws\\Api\\Parser\\RestXmlParser'];
        $proto = $api->getProtocol();
        if (isset($mapping[$proto])) {
            return new $mapping[$proto]($api);
        }
        if ($proto == 'ec2') {
            return new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Parser\QueryParser($api, null, false);
        }
        throw new \UnexpectedValueException('Unknown protocol: ' . $api->getProtocol());
    }
    /**
     * Get the full name of the service
     *
     * @return string
     */
    public function getServiceFullName()
    {
        return $this->definition['metadata']['serviceFullName'];
    }
    /**
     * Get the service id
     *
     * @return string
     */
    public function getServiceId()
    {
        return $this->definition['metadata']['serviceId'];
    }
    /**
     * Get the API version of the service
     *
     * @return string
     */
    public function getApiVersion()
    {
        return $this->definition['metadata']['apiVersion'];
    }
    /**
     * Get the API version of the service
     *
     * @return string
     */
    public function getEndpointPrefix()
    {
        return $this->definition['metadata']['endpointPrefix'];
    }
    /**
     * Get the signing name used by the service.
     *
     * @return string
     */
    public function getSigningName()
    {
        return $this->definition['metadata']['signingName'] ?: $this->definition['metadata']['endpointPrefix'];
    }
    /**
     * Get the service name.
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->definition['metadata']['serviceIdentifier'];
    }
    /**
     * Get the default signature version of the service.
     *
     * Note: this method assumes "v4" when not specified in the model.
     *
     * @return string
     */
    public function getSignatureVersion()
    {
        return $this->definition['metadata']['signatureVersion'] ?: 'v4';
    }
    /**
     * Get the protocol used by the service.
     *
     * @return string
     */
    public function getProtocol()
    {
        return $this->definition['metadata']['protocol'];
    }
    /**
     * Get the uid string used by the service
     *
     * @return string
     */
    public function getUid()
    {
        return $this->definition['metadata']['uid'];
    }
    /**
     * Check if the description has a specific operation by name.
     *
     * @param string $name Operation to check by name
     *
     * @return bool
     */
    public function hasOperation($name)
    {
        return isset($this['operations'][$name]);
    }
    /**
     * Get an operation by name.
     *
     * @param string $name Operation to retrieve by name
     *
     * @return Operation
     * @throws \InvalidArgumentException If the operation is not found
     */
    public function getOperation($name)
    {
        if (!isset($this->operations[$name])) {
            if (!isset($this->definition['operations'][$name])) {
                throw new \InvalidArgumentException("Unknown operation: {$name}");
            }
            $this->operations[$name] = new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Operation($this->definition['operations'][$name], $this->shapeMap);
        }
        return $this->operations[$name];
    }
    /**
     * Get all of the operations of the description.
     *
     * @return Operation[]
     */
    public function getOperations()
    {
        $result = [];
        foreach ($this->definition['operations'] as $name => $definition) {
            $result[$name] = $this->getOperation($name);
        }
        return $result;
    }
    /**
     * Get all of the service metadata or a specific metadata key value.
     *
     * @param string|null $key Key to retrieve or null to retrieve all metadata
     *
     * @return mixed Returns the result or null if the key is not found
     */
    public function getMetadata($key = null)
    {
        if (!$key) {
            return $this['metadata'];
        }
        if (isset($this->definition['metadata'][$key])) {
            return $this->definition['metadata'][$key];
        }
        return null;
    }
    /**
     * Gets an associative array of available paginator configurations where
     * the key is the name of the paginator, and the value is the paginator
     * configuration.
     *
     * @return array
     * @unstable The configuration format of paginators may change in the future
     */
    public function getPaginators()
    {
        if (!isset($this->paginators)) {
            $res = call_user_func($this->apiProvider, 'paginator', $this->serviceName, $this->apiVersion);
            $this->paginators = isset($res['pagination']) ? $res['pagination'] : [];
        }
        return $this->paginators;
    }
    /**
     * Determines if the service has a paginator by name.
     *
     * @param string $name Name of the paginator.
     *
     * @return bool
     */
    public function hasPaginator($name)
    {
        return isset($this->getPaginators()[$name]);
    }
    /**
     * Retrieve a paginator by name.
     *
     * @param string $name Paginator to retrieve by name. This argument is
     *                     typically the operation name.
     * @return array
     * @throws \UnexpectedValueException if the paginator does not exist.
     * @unstable The configuration format of paginators may change in the future
     */
    public function getPaginatorConfig($name)
    {
        static $defaults = ['input_token' => null, 'output_token' => null, 'limit_key' => null, 'result_key' => null, 'more_results' => null];
        if ($this->hasPaginator($name)) {
            return $this->paginators[$name] + $defaults;
        }
        throw new \UnexpectedValueException("There is no {$name} " . "paginator defined for the {$this->serviceName} service.");
    }
    /**
     * Gets an associative array of available waiter configurations where the
     * key is the name of the waiter, and the value is the waiter
     * configuration.
     *
     * @return array
     */
    public function getWaiters()
    {
        if (!isset($this->waiters)) {
            $res = call_user_func($this->apiProvider, 'waiter', $this->serviceName, $this->apiVersion);
            $this->waiters = isset($res['waiters']) ? $res['waiters'] : [];
        }
        return $this->waiters;
    }
    /**
     * Determines if the service has a waiter by name.
     *
     * @param string $name Name of the waiter.
     *
     * @return bool
     */
    public function hasWaiter($name)
    {
        return isset($this->getWaiters()[$name]);
    }
    /**
     * Get a waiter configuration by name.
     *
     * @param string $name Name of the waiter by name.
     *
     * @return array
     * @throws \UnexpectedValueException if the waiter does not exist.
     */
    public function getWaiterConfig($name)
    {
        // Error if the waiter is not defined
        if ($this->hasWaiter($name)) {
            return $this->waiters[$name];
        }
        throw new \UnexpectedValueException("There is no {$name} waiter " . "defined for the {$this->serviceName} service.");
    }
    /**
     * Get the shape map used by the API.
     *
     * @return ShapeMap
     */
    public function getShapeMap()
    {
        return $this->shapeMap;
    }
}
