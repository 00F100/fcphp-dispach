<?php

namespace FcPhp\Dispach\Interfaces
{
    use FcPhp\Di\Interfaces\IDi;
    
    interface IDispach
    {
        /**
         * Method to construct instance of Dispach
         *
         * @param FcPhp\Di\Interfaces\IDi $di Instance of Di
         * @return void
         */
        public function __construct(IDi $di);

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
        public function dispach(string $action, array $params = []);

        /**
         * Method to configure callback
         *
         * @param string $name Name of callback
         * @param object $callback Callback to execute
         * @return void
         */
        public function callback(string $name, object $callback) :void;
    }
}
