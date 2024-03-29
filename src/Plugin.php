<?php

namespace Lidmo\WP\Foundation;

use Lidmo\WP\Foundation\Contracts\Plugin as PluginContract;

class Plugin extends Container implements PluginContract
{
    protected $path;
    protected $url;

    protected $slug;
    protected $databasePath;
    protected $templatePath;
    private $pluginData;

    public function __construct($file, $databasePath = 'database/', $templatePath = 'templates/')
    {
        $this->pluginData = get_plugin_data($file);
        $this->path = plugin_dir_path($file);
        $this->url = plugin_dir_url($file);
        $this->slug = dirname(plugin_basename($file));
        $this->databasePath = $this->path . ltrim($databasePath, '/');
        $this->templatePath = $this->path . ltrim($templatePath, '/');
        $this->registerBaseBindings();
        $this->registerCoreContainerAliases();
    }

    public function getPluginData(string $key = '')
    {
        if ($key !== '' && isset($this->pluginData[$key])) {
            return $this->pluginData[$key];
        }
        return $this->pluginData;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function slug(): string
    {
        return $this->slug;
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

        $this->instance($this->slug(), $this);

        $this->instance(Container::class, $this);

    }

    protected function registerCoreContainerAliases()
    {
        foreach ([
                     $this->slug() => [self::class, \Lidmo\WP\Foundation\Contracts\Container::class, \Lidmo\WP\Foundation\Contracts\Plugin::class, \Psr\Container\ContainerInterface::class],
                 ] as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->alias($key, $alias);
            }
        }
    }
}