<?php

use PHPUnit\Framework\TestCase;
use FcPhp\Dispach\Dispach;
use FcPhp\Dispach\Interfaces\IDispach;
use FcPhp\Controller\Controller;

class DispachUnitTest extends TestCase
{
    public function setUp()
    {
        $instance = new TestMock();

        $this->di = $this->createMock('FcPhp\Di\Interfaces\IDi');
        $this->di
            ->expects($this->any())
            ->method('has')
            ->will($this->returnValue(true));
        $this->di
            ->expects($this->any())
            ->method('make')
            ->will($this->returnValue($instance));

        $this->instance = new Dispach($this->di);
    }

    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof IDispach);
    }

    public function testDispach()
    {
        $this->assertEquals($this->instance->dispach('action@method', ['param' => 'value']), ['value']);
    }

    /**
     * @expectedException FcPhp\Dispach\Exceptions\DispachConfigurationErrorException
     */
    public function testDispachConfigurationErrorException()
    {
        $this->instance->dispach('action', ['param' => 'value']);
    }

    /**
     * @expectedException FcPhp\Dispach\Exceptions\DispachControllerNotFoundException
     */
    public function testDispachControllerNotFoundException()
    {
        $di = $this->createMock('FcPhp\Di\Interfaces\IDi');
        $di
            ->expects($this->any())
            ->method('has')
            ->will($this->returnValue(false));
        $instance = new Dispach($di);
        $instance->dispach('action@method', ['param' => 'value']);
    }

    /**
     * @expectedException FcPhp\Dispach\Exceptions\DispachMethodNotFoundException
     */
    public function testDispachMethodNotFoundException()
    {
        $mock = new TestMock();
        $di = $this->createMock('FcPhp\Di\Interfaces\IDi');
        $di
            ->expects($this->any())
            ->method('has')
            ->will($this->returnValue(true));
        $di
            ->expects($this->any())
            ->method('make')
            ->will($this->returnValue($mock));
        $instance = new Dispach($di);
        $instance->dispach('action@methodtest', ['param' => 'value']);
    }

    public function testDispachCallback()
    {
        $mock = new TestMock();
        $di = $this->createMock('FcPhp\Di\Interfaces\IDi');
        $di
            ->expects($this->any())
            ->method('has')
            ->will($this->returnValue(true));
        $di
            ->expects($this->any())
            ->method('make')
            ->will($this->returnValue($mock));
        $instance = new Dispach($di);
        $instance->callback('dispachCallback', function(array $action, array $params) {
            $this->assertEquals($action, ['action', 'method']);
            $this->assertEquals($params, ['param' => 'value']);
        });
        $instance->callback('dispachConfigurationCallback', function(array $action, string $controller, string $method, array $params) {
            $this->assertEquals($action, ['action', 'method']);
            $this->assertEquals($controller, 'action');
            $this->assertEquals($method, 'method');
            $this->assertEquals($params, ['param' => 'value']);
        });
        $instance->callback('dispachControllerCallback', function(array $action, string $controller, string $method, array $params, $instance) {
            $this->assertEquals($action, ['action', 'method']);
            $this->assertEquals($controller, 'action');
            $this->assertEquals($method, 'method');
            $this->assertEquals($params, ['param' => 'value']);
            $this->assertInstanceOf(TestMock::class, $instance);
        });
        $instance->callback('dispachMethodCallback', function(array $action, string $controller, string $method, array $params, $instance, $result) {
            $this->assertEquals($action, ['action', 'method']);
            $this->assertEquals($controller, 'action');
            $this->assertEquals($method, 'method');
            $this->assertEquals($params, ['param' => 'value']);
            $this->assertInstanceOf(TestMock::class, $instance);
            $this->assertEquals($result, ['value']);
        });
        $instance->dispach('action@method', ['param' => 'value']);
    }

    /**
     * @expectedException FcPhp\Dispach\Exceptions\ControllerNotValidException
     */
    public function testControllerNotValidException()
    {
        $mock = new TestMockNonController();
        $di = $this->createMock('FcPhp\Di\Interfaces\IDi');
        $di
            ->expects($this->any())
            ->method('has')
            ->will($this->returnValue(true));
        $di
            ->expects($this->any())
            ->method('make')
            ->will($this->returnValue($mock));
        $instance = new Dispach($di);
        $instance->dispach('action@methodtest', ['param' => 'value']);
    }
}

class TestMock extends Controller
{
    public function method()
    {
        return func_get_args();
    }
}

class TestMockNonController
{
    public function method()
    {
        return func_get_args();
    }
}
