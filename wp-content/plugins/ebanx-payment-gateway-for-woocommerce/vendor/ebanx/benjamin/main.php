<?php
namespace
{
    use Ebanx\Benjamin\Facade;
    use Ebanx\Benjamin\Models\Configs\AddableConfig;

    if (!function_exists('EBANX')) {
        /**
         * @param AddableConfig $config,... Configuration objects
         * @return Facade EBANX Main Facade
         */
        function EBANX(AddableConfig $config)
        {
            $args = func_get_args();

            $instance = new Facade();
            return call_user_func_array(array($instance, 'addConfig'), $args);
        }
    }
}
