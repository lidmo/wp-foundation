<?php

use Illuminate\Container\Container;

if (!function_exists('lidmo_plugin')) {
    /**
     * Get the available container instance.
     *
     * @param string|null $abstract
     * @param array $parameters
     * @return mixed|\Lidmo\WP\Plugin\Plugin
     */
    function lidmo_plugin($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}