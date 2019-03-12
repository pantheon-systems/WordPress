<?php

class AmeReflectionCallable {
	/**
	 * @var callable
	 */
	private $callback;

	/**
	 * @var ReflectionFunctionAbstract
	 */
	private $reflection;


	/**
	 * AmeReflectionCallable constructor.
	 *
	 * @param callable $callback
	 * @throws ReflectionException
	 */
	public function __construct($callback) {
		$this->callback = $callback;
		$this->reflection = $this->getReflectionFunction($callback);
	}

	/**
	 * @param callable $callback
	 * @return ReflectionFunctionAbstract
	 * @throws ReflectionException
	 */
	private function getReflectionFunction($callback) {
		//Closure or a simple function name.
		if ( $callback instanceof Closure || (is_string($callback) && strpos($callback, '::') === false) ) {
			return new ReflectionFunction($callback);
		}

		if ( is_string($callback) ) {
			//ClassName::method
			$callback = explode('::', $callback, 2);
		} elseif ( is_object($callback) && method_exists($callback, '__invoke') ) {
			//A callable object that has the magical __invoke method.
			$callback = array($callback, '__invoke');
		}

		if ( is_object($callback[0]) ) {
			$reflectionObject = new ReflectionObject($callback[0]);
		} else {
			$reflectionObject = new ReflectionClass($callback[0]);
		}

		$methodName = $callback[1];
		if ( !$reflectionObject->hasMethod($methodName) ) {
			//The callback appears to use magic methods.
			if ( is_string($callback[0]) && $reflectionObject->hasMethod('__callStatic') ) {
				$methodName = '__callStatic';
			} else if (is_object($callback[0]) && $reflectionObject->hasMethod('__call')) {
				$methodName = '__call';
			} else {
				//Probably an invalid callback. It could be a relative static method call,
				//but we don't support those at the moment.
				//See http://php.net/manual/en/language.types.callable.php
			}
		}

		return $reflectionObject->getMethod($methodName);
	}

	/**
	 * Get the file name where the callable was defined.
	 *
	 * May return false for native PHP functions like 'strlen'.
	 *
	 * @return string|false
	 */
	public function getFileName() {
		return $this->reflection->getFileName();
	}
}