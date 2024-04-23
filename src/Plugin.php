<?php

namespace Lidmo\WP\Foundation;

use Lidmo\WP\Foundation\Contracts\Container as ContainerContract;
use Lidmo\WP\Foundation\Contracts\Plugin as PluginContract;

class Plugin implements PluginContract
{
    protected ContainerContract $container;

    protected string $slug;
    protected string $path;
    protected string $url;
    protected string $databasePath;
    protected string $templatePath;
    protected string $routePath;
    private array $pluginData;

    public function __construct(ContainerContract $container, string $file)
    {
        $this->container = $container;

        $this->pluginData = get_plugin_data($file);
        $this->path = plugin_dir_path($file);
        $this->url = plugin_dir_url($file);
        $this->slug = dirname(plugin_basename($file));

        $this->setDatabasePath('database/');
        $this->setTemplatePath('templates/');
        $this->setRoutePath('routes/');

        $this->registerBaseBindings();
        $this->registerCoreContainerAliases();
    }

    public function setDatabasePath(string $databasePath): void
    {
        $this->databasePath = $this->path . ltrim($databasePath, '/');
    }

    public function setTemplatePath(string $templatePath): void
    {
        $this->templatePath = $this->path . ltrim($templatePath, '/');
    }

    public function setRoutePath(string $routePath): void
    {
        $this->routePath = $this->path . ltrim($routePath, '/');
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

    public function routePath(): string
    {
        return $this->routePath;
    }

    protected function registerBaseBindings(): void
    {
        $this->container->instance($this->slug, $this);
        $this->container->instance(ContainerContract::class, $this->container);
    }

    protected function registerCoreContainerAliases(): void
    {
        $coreAliases = [
            $this->slug => [
                PluginContract::class,
                ContainerContract::class,
            ],
        ];

        foreach ($coreAliases as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->container->alias($key, $alias);
            }
        }
    }
}