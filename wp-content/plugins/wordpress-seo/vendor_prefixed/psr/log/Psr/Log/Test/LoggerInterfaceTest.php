<?php

namespace YoastSEO_Vendor\Psr\Log\Test;

use YoastSEO_Vendor\Psr\Log\LoggerInterface;
use YoastSEO_Vendor\Psr\Log\LogLevel;
/**
 * Provides a base test class for ensuring compliance with the LoggerInterface.
 *
 * Implementors can extend the class and implement abstract methods to run this
 * as part of their test suite.
 */
abstract class LoggerInterfaceTest extends \YoastSEO_Vendor\PHPUnit_Framework_TestCase
{
    /**
     * @return LoggerInterface
     */
    public abstract function getLogger();
    /**
     * This must return the log messages in order.
     *
     * The simple formatting of the messages is: "<LOG LEVEL> <MESSAGE>".
     *
     * Example ->error('Foo') would yield "error Foo".
     *
     * @return string[]
     */
    public abstract function getLogs();
    public function testImplements()
    {
        $this->assertInstanceOf('YoastSEO_Vendor\\Psr\\Log\\LoggerInterface', $this->getLogger());
    }
    /**
     * @dataProvider provideLevelsAndMessages
     */
    public function testLogsAtAllLevels($level, $message)
    {
        $logger = $this->getLogger();
        $logger->{$level}($message, array('user' => 'Bob'));
        $logger->log($level, $message, array('user' => 'Bob'));
        $expected = array($level . ' message of level ' . $level . ' with context: Bob', $level . ' message of level ' . $level . ' with context: Bob');
        $this->assertEquals($expected, $this->getLogs());
    }
    public function provideLevelsAndMessages()
    {
        return array(\YoastSEO_Vendor\Psr\Log\LogLevel::EMERGENCY => array(\YoastSEO_Vendor\Psr\Log\LogLevel::EMERGENCY, 'message of level emergency with context: {user}'), \YoastSEO_Vendor\Psr\Log\LogLevel::ALERT => array(\YoastSEO_Vendor\Psr\Log\LogLevel::ALERT, 'message of level alert with context: {user}'), \YoastSEO_Vendor\Psr\Log\LogLevel::CRITICAL => array(\YoastSEO_Vendor\Psr\Log\LogLevel::CRITICAL, 'message of level critical with context: {user}'), \YoastSEO_Vendor\Psr\Log\LogLevel::ERROR => array(\YoastSEO_Vendor\Psr\Log\LogLevel::ERROR, 'message of level error with context: {user}'), \YoastSEO_Vendor\Psr\Log\LogLevel::WARNING => array(\YoastSEO_Vendor\Psr\Log\LogLevel::WARNING, 'message of level warning with context: {user}'), \YoastSEO_Vendor\Psr\Log\LogLevel::NOTICE => array(\YoastSEO_Vendor\Psr\Log\LogLevel::NOTICE, 'message of level notice with context: {user}'), \YoastSEO_Vendor\Psr\Log\LogLevel::INFO => array(\YoastSEO_Vendor\Psr\Log\LogLevel::INFO, 'message of level info with context: {user}'), \YoastSEO_Vendor\Psr\Log\LogLevel::DEBUG => array(\YoastSEO_Vendor\Psr\Log\LogLevel::DEBUG, 'message of level debug with context: {user}'));
    }
    /**
     * @expectedException \Psr\Log\InvalidArgumentException
     */
    public function testThrowsOnInvalidLevel()
    {
        $logger = $this->getLogger();
        $logger->log('invalid level', 'Foo');
    }
    public function testContextReplacement()
    {
        $logger = $this->getLogger();
        $logger->info('{Message {nothing} {user} {foo.bar} a}', array('user' => 'Bob', 'foo.bar' => 'Bar'));
        $expected = array('info {Message {nothing} Bob Bar a}');
        $this->assertEquals($expected, $this->getLogs());
    }
    public function testObjectCastToString()
    {
        if (\method_exists($this, 'createPartialMock')) {
            $dummy = $this->createPartialMock('YoastSEO_Vendor\\Psr\\Log\\Test\\DummyTest', array('__toString'));
        } else {
            $dummy = $this->getMock('YoastSEO_Vendor\\Psr\\Log\\Test\\DummyTest', array('__toString'));
        }
        $dummy->expects($this->once())->method('__toString')->will($this->returnValue('DUMMY'));
        $this->getLogger()->warning($dummy);
        $expected = array('warning DUMMY');
        $this->assertEquals($expected, $this->getLogs());
    }
    public function testContextCanContainAnything()
    {
        $context = array('bool' => \true, 'null' => null, 'string' => 'Foo', 'int' => 0, 'float' => 0.5, 'nested' => array('with object' => new \YoastSEO_Vendor\Psr\Log\Test\DummyTest()), 'object' => new \DateTime(), 'resource' => \fopen('php://memory', 'r'));
        $this->getLogger()->warning('Crazy context data', $context);
        $expected = array('warning Crazy context data');
        $this->assertEquals($expected, $this->getLogs());
    }
    public function testContextExceptionKeyCanBeExceptionOrOtherValues()
    {
        $logger = $this->getLogger();
        $logger->warning('Random message', array('exception' => 'oops'));
        $logger->critical('Uncaught Exception!', array('exception' => new \LogicException('Fail')));
        $expected = array('warning Random message', 'critical Uncaught Exception!');
        $this->assertEquals($expected, $this->getLogs());
    }
}
class DummyTest
{
    public function __toString()
    {
    }
}
