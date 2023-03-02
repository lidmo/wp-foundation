<?php

use Illuminate\Container\Container;

require_once ABSPATH . 'wp-admin/includes/plugin.php';

if (!function_exists('lidmo_plugin')) {
    /**
     * Get the available container instance.
     *
     * @param string|null $abstract
     * @param array $parameters
     * @return mixed|\Lidmo\WP\Foundation\Plugin
     */
    function lidmo_plugin($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}