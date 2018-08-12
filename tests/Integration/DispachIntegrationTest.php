<?php

use PHPUnit\Framework\TestCase;
use FcPhp\Dispach\Dispach;
use FcPhp\Dispach\Interfaces\IDispach;
use FcPhp\Di\Facades\DiFacade;

class DispachIntegrationTest extends TestCase
{
    public function setUp()
    {
        $this->di = DiFacade::getInstance();
        $this->di->set('action', 'TestMockIntegration');

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
        $di = DiFacade::getInstance();
        $instance = new Dispach($di);
        $instance->dispach('actiontest@method', ['param' => 'value']);
    }

    /**
     * @expectedException FcPhp\Dispach\Exceptions\DispachMethodNotFoundException
     */
    public function testDispachMethodNotFoundException()
    {
        $di = DiFacade::getInstance();
        $di->set('action', 'TestMockIntegration');
        $instance = new Dispach($di);
        $instance->dispach('action@methodtest', ['param' => 'value']);
    }

    public function testDispachCallback()
    {
        $di = DiFacade::getInstance();
        $di->set('action', 'TestMockIntegration');

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
            $this->assertInstanceOf(TestMockIntegration::class, $instance);
        });
        $instance->callback('dispachMethodCallback', function(array $action, string $controller, string $method, array $params, $instance, $result) {
            $this->assertEquals($action, ['action', 'method']);
            $this->assertEquals($controller, 'action');
            $this->assertEquals($method, 'method');
            $this->assertEquals($params, ['param' => 'value']);
            $this->assertInstanceOf(TestMockIntegration::class, $instance);
            $this->assertEquals($result, ['value']);
        });
        $instance->dispach('action@method', ['param' => 'value']);
    }
}

class TestMockIntegration
{
    public function method()
    {
        return func_get_args();
    }
}
