<?php

namespace FcPhp\Dispach
{
    use FcPhp\Di\Interfaces\IDi;
    use FcPhp\Dispach\Interfaces\IDispach;
    use FcPhp\Dispach\Exceptions\DispachMethodNotFoundException;
    use FcPhp\Dispach\Exceptions\DispachControllerNotFoundException;
    use FcPhp\Dispach\Exceptions\DispachConfigurationErrorException;

    class Dispach implements IDispach
    {
        /**
         * @var FcPhp\Di\Interfaces\IDi
         */
        private $di;

        /**
         * @var object
         */
        private $dispachCallback;

        /**
         * @var object
         */
        private $dispachConfigurationCallback;

        /**
         * @var object
         */
        private $dispachControllerCallback;

        /**
         * @var object
         */
        private $dispachMethodCallback;

        /**
         * Method to construct instance of Dispach
         *
         * @param FcPhp\Di\Interfaces\IDi $di Instance of Di
         * @return void
         */
        public function __construct(IDi $di)
        {
            $this->di = $di;
        }

        /**
         * Method to execute controller
         *
         * @param string $action Action to execute, ex: controller@method
         * @param array $params Params to method
         * @throws FcPhp\Dispach\Exceptions\DispachConfigurationErrorException
         * @throws FcPhp\Dispach\Exceptions\DispachControllerNotFoundException
         * @throws FcPhp\Dispach\Exceptions\DispachMethodNotFoundException
         * @return mixed
         */
        public function dispach(string $action, array $params = [])
        {
            $action = explode('@', $action);
            $this->dispachCallback($action, $params);
            if(count($action) == 2) {
                $controller = current($action);
                $method = end($action);
                $this->dispachConfigurationCallback($action, $controller, $method, $params);
                if($this->di->has($controller)) {
                    $instance = $this->di->make($controller);
                    $this->dispachControllerCallback($action, $controller, $method, $params, $instance);
                    if(method_exists($instance, $method)) {
                        $result = call_user_func_array([$instance, $method], $params);
                        $this->dispachMethodCallback($action, $controller, $method, $params, $instance, $result);
                        return $result;
                    }
                    throw new DispachMethodNotFoundException();
                }
                throw new DispachControllerNotFoundException();
            }
            throw new DispachConfigurationErrorException();
        }

        /**
         * Method to configure callback
         *
         * @param string $name Name of callback
         * @param object $callback Callback to execute
         * @return void
         */
        public function callback(string $name, object $callback) :void
        {
            if(property_exists($this, $name)) {
                $this->{$name} = $callback;
            }
        }

        /**
         * Method to execute callback
         *
         * @param array $action Action to execute
         * @param array $params Params to method
         * @return void
         */
        private function dispachCallback(array $action, array $params) :void
        {
            $dispachCallback = $this->dispachCallback;
            if(is_object($dispachCallback)) {
                $dispachCallback($action, $params);
            }
        }

        /**
         * Method to execute callback of configuration true
         *
         * @param array $action Action to execute
         * @param string $controller Controller to execute
         * @param string $method Method to execute
         * @param array $params Params to method
         * @return void
         */
        private function dispachConfigurationCallback(array $action, string $controller, string $method, array $params) :void
        {
            $dispachConfigurationCallback = $this->dispachConfigurationCallback;
            if(is_object($dispachConfigurationCallback)) {
                $dispachConfigurationCallback($action, $controller, $method, $params);
            }
        }

        /**
         * Method to execute callback of controller true
         *
         * @param array $action Action to execute
         * @param string $controller Controller to execute
         * @param string $method Method to execute
         * @param array $params Params to method
         * @param mixed $instance Instance of controller
         * @return void
         */
        private function dispachControllerCallback(array $action, string $controller, string $method, array $params, $instance) :void
        {
            $dispachControllerCallback = $this->dispachControllerCallback;
            if(is_object($dispachControllerCallback)) {
                $dispachControllerCallback($action, $controller, $method, $params, $instance);
            }
        }

        /**
         * Method to execute callback of method true
         *
         * @param array $action Action to execute
         * @param string $controller Controller to execute
         * @param string $method Method to execute
         * @param array $params Params to method
         * @param mixed $instance Instance of controller
         * @param mixed $result Result of execution controller
         * @return void
         */
        private function dispachMethodCallback(array $action, string $controller, string $method, array $params, $instance, $result) :void
        {
            $dispachMethodCallback = $this->dispachMethodCallback;
            if(is_object($dispachMethodCallback)) {
                $dispachMethodCallback($action, $controller, $method, $params, $instance, $result);
            }
        }
    }
}
