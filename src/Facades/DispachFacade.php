<?php

namespace FcPhp\Dispach\Facades
{
    use FcPhp\Dispach\Dispach;
    use FcPhp\Di\Facades\DiFacade;
    use FcPhp\Dispach\Interfaces\IDispach;

    class DispachFacade
    {
        /**
         * @var FcPhp\Dispach\Interfaces\IDispach
         */
        private static $instance;

        /**
         * Method to return instance of Dispach
         *
         * @return FcPhp\Dispach\Interfaces\IDispach
         */
        public static function getInstance() :IDispach
        {
            if(!self::$instance instanceof IDispach) {
                self::$instance = new Dispach(DiFacade::getInstance());
            }
            return self::$instance;
        }
    }
}
