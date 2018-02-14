<?php

namespace Wpae\Di;


class WpaeDi
{
    private $config;

    private $cache;

    private $services;

    public function __construct($config)
    {

        $this->config = $config;

        $this->services = [
            'c' => [
                'value' => 10
            ],
            'a' => [
                'class' => '\Wpae\ClassA',
                'arguments' => [
                    '@a',
                    '@b'
                ]
            ],
            'b' => [
                'class' => '\Wpae\ClassB',
                'arguments' => [
                    '@a',
                    '%c'
                ]
            ]
        ];

    }

    public function get($name) {
        if(isset($this->cache[$name])) {
            return $this->cache[$name];
        }
        if(isset($this->services[$name])) {
            $arguments = [];
            if(count($this->services[$name]['arguments'])){
                foreach($this->services[$name]['arguments'] as $argument) {
                    $arguments[] = $this->get($argument);
                }
            } else {
                $serviceClass = $this->services[$name]['class'];
                $this->cache[$name] = new $serviceClass();
                return $this->cache[$name];
            }
            $r = new \ReflectionClass($this->services[$name]['class']);
            $this->cache[$name] = $r->newInstanceArgs($arguments);
        }
        return $this->cache[$name];
    }
}