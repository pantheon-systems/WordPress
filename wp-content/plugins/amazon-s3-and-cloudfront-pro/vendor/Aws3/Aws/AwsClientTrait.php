<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws;

use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Service;
/**
 * A trait providing generic functionality for interacting with Amazon Web
 * Services. This is meant to be used in classes implementing
 * \Aws\AwsClientInterface
 */
trait AwsClientTrait
{
    public function getPaginator($name, array $args = [])
    {
        $config = $this->getApi()->getPaginatorConfig($name);
        return new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\ResultPaginator($this, $name, $args, $config);
    }
    public function getIterator($name, array $args = [])
    {
        $config = $this->getApi()->getPaginatorConfig($name);
        if (!$config['result_key']) {
            throw new \UnexpectedValueException(sprintf('There are no resources to iterate for the %s operation of %s', $name, $this->getApi()['serviceFullName']));
        }
        $key = is_array($config['result_key']) ? $config['result_key'][0] : $config['result_key'];
        if ($config['output_token'] && $config['input_token']) {
            return $this->getPaginator($name, $args)->search($key);
        }
        $result = $this->execute($this->getCommand($name, $args))->search($key);
        return new \ArrayIterator((array) $result);
    }
    public function waitUntil($name, array $args = [])
    {
        return $this->getWaiter($name, $args)->promise()->wait();
    }
    public function getWaiter($name, array $args = [])
    {
        $config = isset($args['@waiter']) ? $args['@waiter'] : [];
        $config += $this->getApi()->getWaiterConfig($name);
        return new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Waiter($this, $name, $args, $config);
    }
    public function execute(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\CommandInterface $command)
    {
        return $this->executeAsync($command)->wait();
    }
    public function executeAsync(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\CommandInterface $command)
    {
        $handler = $command->getHandlerList()->resolve();
        return $handler($command);
    }
    public function __call($name, array $args)
    {
        $params = isset($args[0]) ? $args[0] : [];
        if (substr($name, -5) === 'Async') {
            return $this->executeAsync($this->getCommand(substr($name, 0, -5), $params));
        }
        return $this->execute($this->getCommand($name, $params));
    }
    /**
     * @param string $name
     * @param array $args
     *
     * @return CommandInterface
     */
    public abstract function getCommand($name, array $args = []);
    /**
     * @return Service
     */
    public abstract function getApi();
}
