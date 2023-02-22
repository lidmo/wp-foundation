<?php

namespace Lidmo\WP\Foundation\Hooks;

use Lidmo\WP\Foundation\Contracts\Kernel as HooksKernel;
use Lidmo\WP\Foundation\Contracts\Plugin;

class Kernel implements HooksKernel
{
    protected $plugin;
    protected $hooks = [];

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function run(): void
    {
        foreach ($this->hooks as $hook){
            if (!is_string($hook)) {
                continue;
            }

            [$name] = $this->parseHook($hook);

            $instance = $this->plugin->make($name);

            if (method_exists($instance, 'handle')) {
                $call = 'add_' . $instance->getType();
                $call($instance->getName(), [$instance, 'handle'], $instance->getPriority(), $instance->getAcceptedArgs());
            }
        }
    }

    protected function parseHook($hook): array
    {
        [$name, $parameters] = array_pad(explode(':', $hook, 2), 2, []);

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return [$name, $parameters];
    }
}