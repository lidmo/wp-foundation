<?php

namespace Lidmo\WP\Foundation;

use Illuminate\Container\Container;
use Lidmo\WP\Foundation\Contracts\Plugin as PluginContract;

class Plugin extends Container implements PluginContract
{
    protected $version;

    protected $name;

    protected $path;
    protected $url;

    protected $databasePath;

    protected $templatePath;

    public function __construct($name, $file = __FILE__, $version = '1.0.0', $databasePath = 'database/', $templatePath = 'templates/')
    {
        $this->version = $version;
        $this->name = $name;
        $this->path = plugin_dir_path($file);
        $this->url = plugin_dir_url($file);
        $this->databasePath = $this->path . ltrim($databasePath, '/');
        $this->templatePath = $this->path . ltrim($templatePath, '/');
        $this->registerBaseBindings();
        $this->registerCoreContainerAliases();
    }


    public function version(): string
    {
        return $this->version;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function databasePath(): string
    {
        return $this->databasePath;
    }

    public function templatePath(): string
    {
        return $this->templatePath;
    }

    protected function registerBaseBindings()
    {
        static::setInstance($this);

        $this->instance($this->name(), $this);

        $this->instance(Container::class, $this);

        $this->instance('log', new Logger($this));
    }

    public function registerCoreContainerAliases()
    {
        foreach ([
                     $this->name() => [self::class, \Illuminate\Contracts\Container\Container::class, \Lidmo\WP\Foundation\Contracts\Plugin::class, \Psr\Container\ContainerInterface::class],
                 ] as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->alias($key, $alias);
            }
        }
    }
}